"use strict";
//http://api.sr.se/api/v2/traffic/messages?format=json
//json/SRInfo.json
var App = {
    url: "json/SRInfo.json",
    category: 4,
    htmlArray: [],
    messageArray: [],
    markers: [],

    init: function() {
        if(!jQuery.isEmptyObject(App.htmlArray)) {
            App.htmlArray.length = 0;
        }
        if(!jQuery.isEmptyObject(App.messageArray)) {
            App.messageArray.length = 0;
        }
        if(!jQuery.isEmptyObject(App.markers)) {
            App.markers.length = 0;
        }
        var jsonArray = [];
        //Hämtar trafikinfo
        new AjaxCon(App.url, function(data) {
            var i, messageObj = JSON.parse(data);
            for(i = 0; i < messageObj.messages.length; i += 1) {
                var message = messageObj.messages[i];
                jsonArray.push(message);
            }
            //Filtrerar trafikmeddelanden på kategori
            if(parseInt(App.category) === 4) {
                App.messageArray = jsonArray;
            }else {
                App.messageArray = $.grep(jsonArray, function(obj) {
                    return obj.category === parseInt(App.category);
                });
            }
            App.initMap();
        });
    },

    initMap: function() {
        var prevInfowindow = false, dl = document.getElementById("infoList");
        //Initialiserar kartan
        var map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 62, lng: 15 },
            zoom: 5
        });
        var formatandSortArray = function() {
            //Formaterar datum och kategori
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
            }
            //Sorterar trafikmeddelandena efter datum
            App.messageArray.sort(function (a, b) {
                return b.createddate - a.createddate;
            });
        };
        //Skapar infowindows och öppnar och stänger dem när man klickar på en markör
        //http://stackoverflow.com/questions/5868903/marker-content-infowindow-google-maps
        //http://stackoverflow.com/questions/2223574/google-maps-auto-close-open-infowindows
        var addInfowindow = function(marker, contentString) {
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            marker.addListener('click', function() {
                if(prevInfowindow) {
                    prevInfowindow.close();
                }
                prevInfowindow = infowindow;
                infowindow.open(map, marker);
            });
        };
        //Skapar markörer
        var createMarkers = function() {
            var i;
            for(i = 0; i < App.messageArray.length; i += 1) {
                var latLng = {lat: App.messageArray[i].latitude, lng: App.messageArray[i].longitude};

                var contentString = "<dl class='dl-horizontal'><dt>Titel:</dt><dd>"
                    + App.messageArray[i].title + "</dd><dt>Läge:</dt><dd>"
                    + App.messageArray[i].exactlocation + "</dd><dt>Datum:</dt><dd>"
                    + App.messageArray[i].createddate.format('d-m-Y') + "</dd><dt>Beskrivning:</dt><dd>"
                    + App.messageArray[i].description + "</dd><dt>Kategori:</dt><dd>"
                    + App.messageArray[i].category + "</dd></dl>";

                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: App.messageArray[i].title
                });
                addInfowindow(marker, contentString);
                App.markers.push(marker);
            }
        };
        //Får markören att hoppa när man klickar på en länk i listan
        var markerBounce = function(aTag) {
            var markers = $.grep(App.markers, function(obj) { return obj.title === aTag.innerHTML });
            markers[0].setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(function() { markers[0].setAnimation(null); }, 2000);
        };
        //Skapar lista med trafikmeddelanden
        var createList = function() {
            var i, j;
            for(i = 0; i < App.messageArray.length; i += 1) {
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
            for(j = 0; j < App.htmlArray.length; j += 1) {
                dl.appendChild(App.htmlArray[j]);
            }
        };
        //Gör ett nytt anrop när man klickar på en länk i dropdown-listan
        var changeCategory = function(link) {
            var i, j;
            for(i = 0; i < App.markers.length; i += 1) {
                App.markers[i].setMap(null);
            }
            for(j = 0; j < App.htmlArray.length; j += 1) {
                dl.removeChild(App.htmlArray[j]);
            }
            /*if(parseInt(link.id) === 4) {
                App.url = "http://api.sr.se/api/v2/traffic/messages?format=json";
            }else {
                App.url = "http://api.sr.se/api/v2/traffic/messages?format=json&category=" + link.id;
            }*/
            App.category = link.id;
            App.init();
        };
        //Skapar eventhandlers för länkarna i dropdown-listan
        var addListEventHandlers = function() {
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