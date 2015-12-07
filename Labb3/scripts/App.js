"use strict";
//http://stackoverflow.com/questions/5868903/marker-content-infowindow-google-maps
//http://stackoverflow.com/questions/2223574/google-maps-auto-close-open-infowindows
//http://api.sr.se/api/v2/traffic/messages?format=json
var App = {
    htmlArray: [],
    messageArray: [],
    markers: [],

    init: function () {
        //Hämtar trafikinfo
        new AjaxCon("json/SRInfo.json", function (data) {
            var i, messageObj = JSON.parse(data);
            for (i = 0; i < messageObj.messages.length; i += 1) {
                var message = messageObj.messages[i];
                App.messageArray.push(message);
            }
            App.initMap();
        });
    },

    initMap: function () {
        var i, j, k, l, dl = document.getElementById("infoList");
        //Formaterar datum och kategori
        for(i = 0; i < App.messageArray.length; i += 1){
            App.messageArray[i].date = new Date(parseInt(App.messageArray[i].createddate.substring(6, 19)));
            if(App.messageArray[i].category === 0){
                App.messageArray[i].category = "Vägtrafik";
            } else if(App.messageArray[i].category === 1){
                App.messageArray[i].category = "Kollektivtrafik";
            } else if(App.messageArray[i].category === 2) {
                App.messageArray[i].category = "Planerad störning";
            } else if(App.messageArray[i].category === 3) {
                App.messageArray[i].category = "Övrigt";
            }
        }
        //Sorterar trafikmeddelandena efter datum
        App.messageArray.sort(function (a, b) {
            return b.date - a.date;
        });
        //Initialiserar kartan
        var map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 62, lng: 15 },
            zoom: 5
        });
        //Skapar infowindows och öppnar och stänger dem när man klickar på en markör
        var prevInfowindow = false;
        var addInfowindow = function(marker, contentString) {
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            marker.addListener('click', function () {
                if(prevInfowindow) {
                    prevInfowindow.close();
                }
                prevInfowindow = infowindow;
                infowindow.open(map, marker);
            });
        };
        //Skapar markörer
        for (l = 0; l < App.messageArray.length; l += 1) {
            var latLng = {lat: App.messageArray[l].latitude, lng: App.messageArray[l].longitude};
            var contentString = "<dl class='dl-horizontal'><dt>Titel:</dt><dd>"
                + App.messageArray[l].title + "</dd><dt>Läge:</dt><dd>"
                + App.messageArray[l].exactlocation + "</dd><dt>Datum:</dt><dd>"
                + App.messageArray[l].date.format('d-m-Y') + "</dd><dt>Beskrivning:</dt><dd>"
                + App.messageArray[l].description + "</dd><dt>Kategori:</dt><dd>"
                + App.messageArray[l].category + "</dd></dl>";

            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: App.messageArray[l].title
            });

            addInfowindow(marker, contentString);
            App.markers.push(marker);
        }
        //Får markören att hoppa när man klickar på en länk i listan
        var markerBounce = function(aTag) {
            var markers = $.grep(App.markers, function(e) { return e.title === aTag.innerHTML });
            var marker = markers[0];
            marker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(function(){
                marker.setAnimation(null);
            }, 2000);
        };
        //Skapar lista med trafikmeddelanden
        for(j = 0; j < App.messageArray.length; j += 1) {
            var titleT = document.createElement("dt"), titleD = document.createElement("dd");
            var dateT = document.createElement("dt"), dateD = document.createElement("dd");
            var aTag = document.createElement("a");
            titleT.innerHTML = "Titel:";
            aTag.setAttribute("href", "#");
            aTag.innerHTML =  App.messageArray[j].title;
            titleD.appendChild(aTag);
            aTag.onclick = function(e) {
                e.preventDefault();
                markerBounce(e.target);
            };
            dateT.innerHTML = "Datum:";
            dateD.innerHTML = App.messageArray[j].date.format('d-m-Y');
            App.htmlArray.push(titleT, titleD, dateT, dateD);
        }

        for(k = 0; k < App.htmlArray.length; k += 1){
            dl.appendChild(App.htmlArray[k]);
        }

    }
};

window.onload = App.init;