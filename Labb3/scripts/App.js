"use strict";
var App = {
    url: "http://api.sr.se/api/v2/traffic/messages?format=json",
    fileUrl: "json/SRInfo.json",
    category: 4,
    dl: document.getElementById("infoList"),
    htmlArray: [],
    messageArray: [],
    markers: [],

    init: function () {
        App.resetArrays();
        App.createDropdownHandlers();
        App.getTrafficInfo();
    },

    resetArrays: function () {
        var i, j;
        if (!jQuery.isEmptyObject(App.messageArray)) {
            App.messageArray.length = 0;
        }
        if (!jQuery.isEmptyObject(App.markers)) {
            for (i = 0; i < App.markers.length; i += 1) {
                App.markers[i].setMap(null);
            }
            App.markers.length = 0;
        }
        if (!jQuery.isEmptyObject(App.htmlArray)) {
            for (j = 0; j < App.htmlArray.length; j += 1) {
                App.dl.removeChild(App.htmlArray[j]);
            }
            App.htmlArray.length = 0;
        }
    },

    createDropdownHandlers: function () {
        var i, links = $('#dropdown').find('a');
        //Gör ett nytt anrop när man klickar på en länk i dropdown-listan
        var changeCategory = function (link) {
            App.category = link.id;
            App.resetArrays();
            App.getTrafficInfo();
        };
        //Skapar eventhandlers för länkarna i dropdown-listan
        for (i = 0; i < links.length; i += 1) {
            links[i].onclick = function (e) {
                e.preventDefault();
                changeCategory(e.target);
            }
        }
    },

    getTrafficInfo: function () {
        //Hämtar header från json-filen
        //http://osric.com/chris/accidental-developer/2014/08/using-getresponseheader-with-jquerys-ajax-method/
        $.ajax({
            type: 'HEAD',
            url: App.fileUrl
        }).done(function (data, textStatus, xhr) {
            var hdr = xhr.getResponseHeader('last-modified');
            var lastMod = new Date(hdr);
            //Hämtar trafikinfo
            var getInfo = function (url, fromAPI) {
                new AjaxCon(url, function (data) {
                    var messageObj = JSON.parse(data);
                    //Filtrerar trafikmeddelanden på kategori
                    var filterMessages = function () {
                        var i, messArray = [];
                        for (i = 0; i < messageObj.messages.length; i += 1) {
                            var message = messageObj.messages[i];
                            messArray.push(message);
                        }
                        if (parseInt(App.category) === 4) {
                            App.messageArray = messArray;
                        } else {
                            App.messageArray = $.grep(messArray, function (obj) {
                                return obj.category === parseInt(App.category);
                            });
                        }
                    };
                    //Skapar karta och/eller lista
                    var createComponents = function () {
                        if (jQuery.isEmptyObject(App.messageArray)) {
                            App.createNoMessList();
                        } else {
                            App.createMapAndList();
                        }
                    };
                    //Väljer aktion beroende på om datat läses in från fil eller API:et, och beroende på om
                    // anropet till API:et lyckades
                    if (fromAPI === false) {
                        filterMessages();
                        createComponents();
                    } else {
                        if (typeof messageObj === 'object') {
                            //Skriver nytt data till json-filen
                            //http://stackoverflow.com/questions/8599595/send-json-data-from-javascript-to-php
                            $.ajax({
                                type: 'POST',
                                url: 'scripts/caching.php',
                                data: { json: data },
                                dataType: 'json'
                            });
                            filterMessages();
                            createComponents();
                        } else {
                            getInfo(App.fileUrl, false);
                        }
                    }
                });
            };
            //Anropar getInfo() med url till API:et eller json-filen beroende på om filen är äldre än 5 minuter
            //http://stackoverflow.com/questions/15272761/javascript-time-passed-since-timestamp
            if (Math.floor((new Date() - lastMod) / 60000) < 5) {
                getInfo(App.fileUrl, false);
            } else {
                getInfo(App.url, true);
            }
        });
    },

    createNoMessList: function () {
        var i, noMessagesT = document.createElement("dt");
        noMessagesT.innerHTML = "Inga meddelanden";
        App.htmlArray.push(noMessagesT);
        for (i = 0; i < App.htmlArray.length; i += 1) {
            App.dl.appendChild(App.htmlArray[i]);
        }
    },

    createMapAndList: function () {
        var prevInfowindow = false;
        //Initialiserar kartan
        var map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 62.334225, lng: 15.023907 },
            zoom: 5
        });
        //Formaterar datum, kategori och prioritet
        var formatandSortArray = function () {
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
                App.dl.appendChild(App.htmlArray[j]);
            }
        };
        //Anropar funktioner
        formatandSortArray();
        createMarkers();
        createList();
    }
};
window.onload = App.init;