# Reflektionsfrågor

## Krav i API:erna

### Google Maps

Användning av `<!DOCTYPE html>` rekommenderas. Ett div-element med id "map", som innehåller kartan, måste skapas.  
En Javascript-funktion för att skapa kartan måste inkluderas, och API:ets Javascript måste laddas med hjälp av en 
skript-tagg i html-filen. CSS-attributet "height" måste sättas på det div-element som innehåller kartan. I funktionen 
för att skapa kartan måste två "options" inkluderas: "center" och "zoom".

### Sveriges Radio

SR:s API har inga speciella krav utöver att materialet inte får  användas på ett sådant sätt att det skulle kunna skada 
Sveriges Radios oberoende eller trovärdighet.

## Cachning

Jag sparar meddelandena från SR:s API i en JSON-fil, och läser in meddelandena därifrån om det inte har gått mer än 5 
minuter sedan filen ändrades senast. Eftersom meddelandena kan uppdateras ganska ofta cachar jag dem i endast 
5 minuter.

## Säkerhet och stabilitet

Jag validerar på klienten (App.js) och på servern (caching.php) att datat som hämtas från SR:s API är ett JSON-objekt. 
Innan datat skrivs till JSON-filen filtreras det med htmlspecialchars. Efter att datat från API:et har validerats på 
servern och skrivits till filen hämtar jag datat från filen, i stället för att skriva ut datat från API:et direkt.  
Om ett svar från SR:s API inte är ett JSON-objekt läses JSON-filen in istället. Jag har inte implementerat någon 
reservplan om Google Maps skulle sluta fungera.   

## Optimering

Jag har min Javascript-kod och CSS-kod i externa filer, och  jag har minifierat mina Javascript-filer med [Packer]
(http://dean.edwards.name/packer/). Jag använder CDN-tjänster för Bootstrap och Jquery. För att se om JSON-filen är mer 
än 5 minuter gammal läser jag endast in "head". Jag har aktiverat [komprimering med Gzip på servern]
(http://salscode.com/tutorials/2009/10/15/gzip-htaccess/), och placerat CSS-filer längst upp och Javascript-filer längst 
ner i html-filen.