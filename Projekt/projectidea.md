Ingela Bladh (ib222dp)

# Projektidé

Jag planerar att skapa en webbplats där botaniker kan söka i [BHL(Biodiversity Heritage Library)]
(http://www.biodiversitylibrary.org/) och [Gallica](http://gallica.bnf.fr/) samtidigt. I mån av tid kommer jag även att 
lägga till [Google Books](https://books.google.com/). Jag blev inspirerad av [denna bloggpost]
(http://blog.biodiversitylibrary.org/2012/04/bhl-and-our-users-anders-hagborg.html), där Anders Hagborg berättar att 
han skulle vilja kunna söka i bland annat BHL, Gallica och Google Books samtidigt.  
Gallica har inget eget API, men de bidrar med sitt material till [Europeana](http://www.europeana.eu/portal/), så jag 
kommer därför att hämta deras material via Europeanas API. BHL har ett eget API.  
Användare ska kunna välja om de endast vill söka i BHL eller Gallica eller båda samtidigt, och det ska även finnas en 
del möjligheter till avancerad sökning, beroende på de metoder som API-erna erbjuder.  
Jag kommer troligen att skriva applikationen i PHP.