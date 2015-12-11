"use strict";
var App = {
    url: "http://api.sr.se/api/v2/traffic/messages?format=json",
    category: 4,
    htmlArray: [],
    messageArray: [],
    markers: [],

    init: function () {
        var messArray = [], fromAPI;
        if (!jQuery.isEmptyObject(App.htmlArray)) {
            App.htmlArray.length = 0;
        }
        if (!jQuery.isEmptyObject(App.messageArray)) {
            App.messageArray.length = 0;
        }
        if (!jQuery.isEmptyObject(App.markers)) {
            App.markers.length = 0;
        }
        //Hämtar header från json-filen
        //http://osric.com/chris/accidental-developer/2014/08/using-getresponseheader-with-jquerys-ajax-method/
        $.ajax({
            type: 'HEAD',
            url: 'json/SRInfo.json'
        }).done(function (data, textStatus, xhr) {
            var hdr = xhr.getResponseHeader('last-modified');
            var lastMod = new Date(hdr);
            console.log(lastMod);
            //Hämtar trafikinfo
            var getInfo = function (url, fromAPI) {
                new AjaxCon(url, function (data) {
                    var i, messageObj = JSON.parse(data);
                    if (typeof messageObj === 'object') {
                        if (fromAPI === true) {
                            //Skriver nytt data till json-filen
                            //http://stackoverflow.com/questions/8599595/send-json-data-from-javascript-to-php
                            $.ajax({
                                type: 'POST',
                                url: 'scripts/caching.php',
                                data: { json: data },
                                dataType: 'json'
                            });
                        }
                        for (i = 0; i < messageObj.messages.length; i += 1) {
                            var message = messageObj.messages[i];
                            messArray.push(message);
                        }
                        //Filtrerar trafikmeddelanden på kategori
                        if (parseInt(App.category) === 4) {
                            App.messageArray = messArray;
                        } else {
                            App.messageArray = $.grep(messArray, function (obj) {
                                return obj.category === parseInt(App.category);
                            });
                        }
                        App.initMap();
                    }
                });
            };
            //Anropar getInfo() med url till API:et eller json-filen beroende på om filen är äldre än 5 minuter
            //http://stackoverflow.com/questions/15272761/javascript-time-passed-since-timestamp
            if (Math.floor((new Date() - lastMod) / 60000) > 5) {
                fromAPI = true;
                getInfo(App.url, fromAPI);
            } else {
                fromAPI = false;
                getInfo('json/SRInfo.json', fromAPI);
            }
        });
    },

    initMap: function () {
        var prevInfowindow = false, dl = document.getElementById("infoList");
        //Initialiserar kartan
        var map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 62.334225, lng: 15.023907 },
            zoom: 5
        });
        var formatandSortArray = function () {
            //Formaterar datum, kategori och prioritet
            var i;
            for (i = 0; i < App.messageArray.length; i += 1) {
                App.messageArray[i].createddate = new Date(parseInt(App.messageArray[i].createddate.substring(6, 19)));
                if (App.messageArray[i].category === 0) {
                    App.messageArray[i].category = "Vägtrafik";
                } else if (App.messageArray[i].category === 1) {
                    App.messageArray[i].category = "Kollektivtrafik";
                } else if (App.messageArray[i].category === 2) {
                    App.messageArray[i].category = "Planerad störning";
                } else if (App.messageArray[i].category === 3) {
                    App.messageArray[i].category = "Övrigt";
                }
                if (App.messageArray[i].priority === 1) {
                    App.messageArray[i].priorityText = "Mycket allvarlig händelse";
                } else if (App.messageArray[i].priority === 2) {
                    App.messageArray[i].priorityText = "Stor händelse";
                } else if (App.messageArray[i].priority === 3) {
                    App.messageArray[i].priorityText = "Störning";
                } else if (App.messageArray[i].priority === 4) {
                    App.messageArray[i].priorityText = "Information";
                } else if (App.messageArray[i].priority === 5) {
                    App.messageArray[i].priorityText = "Mindre störning";
                }
            }
            //Sorterar trafikmeddelandena efter datum
            App.messageArray.sort(function (a, b) {
                return b.createddate - a.createddate;
            });
        };
        //Skapar infowindows och öppnar och stänger dem när man klickar på en markör
        //http://stackoverflow.com/questions/5868903/marker-content-infowindow-google-maps
        //http://stackoverflow.com/questions/2223574/google-maps-auto-close-open-infowindows
        var addInfowindow = function (marker, contentString) {
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            marker.addListener('click', function () {
                if (prevInfowindow) {
                    prevInfowindow.close();
                }
                prevInfowindow = infowindow;
                infowindow.open(map, marker);
            });
        };
        //Skapar markörer
        var createMarkers = function () {
            var i;
            for (i = 0; i < App.messageArray.length; i += 1) {
                var latLng = { lat: App.messageArray[i].latitude, lng: App.messageArray[i].longitude };

                var contentString = "<dl class='dl-horizontal'><dt>Titel:</dt><dd>"
                    + App.messageArray[i].title + "</dd><dt>Läge:</dt><dd>"
                    + App.messageArray[i].exactlocation + "</dd><dt>Datum:</dt><dd>"
                    + App.messageArray[i].createddate.format('d-m-Y') + "</dd><dt>Beskrivning:</dt><dd>"
                    + App.messageArray[i].description + "</dd><dt>Kategori:</dt><dd>"
                    + App.messageArray[i].category + "</dd><dt>Prioritet:</dt><dd>"
                    + App.messageArray[i].priorityText + "</dd></dl>";

                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: App.messageArray[i].title
                });
                //Byter färg på markören beroende på prioritet
                if (App.messageArray[i].priority === 1) {
                    marker.setIcon('http://maps.google.com/mapfiles/ms/icons/blue-dot.png');
                } else if (App.messageArray[i].priority === 2) {
                    marker.setIcon('http://maps.google.com/mapfiles/ms/icons/purple-dot.png');
                } else if (App.messageArray[i].priority === 3) {
                    marker.setIcon('http://maps.google.com/mapfiles/ms/icons/red-dot.png');
                } else if (App.messageArray[i].priority === 4) {
                    marker.setIcon('http://maps.google.com/mapfiles/ms/icons/yellow-dot.png');
                } else if (App.messageArray[i].priority === 5) {
                    marker.setIcon('http://maps.google.com/mapfiles/ms/icons/green-dot.png');
                }
                addInfowindow(marker, contentString);
                App.markers.push(marker);
            }
        };
        //Får markören att hoppa när man klickar på en länk i listan
        var markerBounce = function (aTag) {
            var markers = $.grep(App.markers, function (obj) { return obj.title === aTag.innerHTML });
            markers[0].setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(function () { markers[0].setAnimation(null); }, 2000);
        };
        //Skapar lista med trafikmeddelanden
        var createList = function () {
            var i, j;
            for (i = 0; i < App.messageArray.length; i += 1) {
                var titleT = document.createElement("dt"), titleD = document.createElement("dd");
                var dateT = document.createElement("dt"), dateD = document.createElement("dd");
                var aTag = document.createElement("a");
                titleT.innerHTML = "Titel:";
                aTag.setAttribute("href", "#");
                aTag.innerHTML = App.messageArray[i].title;
                titleD.appendChild(aTag);
                aTag.onclick = function (e) {
                    e.preventDefault();
                    markerBounce(e.target);
                };
                dateT.innerHTML = "Datum:";
                dateD.innerHTML = App.messageArray[i].createddate.format('d-m-Y');
                App.htmlArray.push(titleT, titleD, dateT, dateD);
            }
            for (j = 0; j < App.htmlArray.length; j += 1) {
                dl.appendChild(App.htmlArray[j]);
            }
        };
        //Gör ett nytt anrop när man klickar på en länk i dropdown-listan
        var changeCategory = function (link) {
            var i, j;
            for (i = 0; i < App.markers.length; i += 1) {
                App.markers[i].setMap(null);
            }
            for (j = 0; j < App.htmlArray.length; j += 1) {
                dl.removeChild(App.htmlArray[j]);
            }
            App.category = link.id;
            App.init();
        };
        //Skapar eventhandlers för länkarna i dropdown-listan
        var addListEventHandlers = function () {
            var i, links = $('#dropdown').find('a');
            for (i = 0; i < links.length; i += 1) {
                links[i].onclick = function (e) {
                    e.preventDefault();
                    changeCategory(e.target);
                }
            }
        };
        //Anropar funktioner
        formatandSortArray();
        createMarkers();
        createList();
        addListEventHandlers();
    }
};
window.onload = App.init;