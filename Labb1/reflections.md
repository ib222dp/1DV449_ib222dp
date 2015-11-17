Reflektionsfrågor

1. Etiska aspekter vid webbskrapning

Man kan t.ex. skrapa information och presentera den på ett sätt som kränker den personliga integriteten. 
Ett svenskt exempel är Lexbase, en webbplats där allmänheten kan söka efter personer och företag som har 
varit föremål för juridisk prövning i svenska domstolar sedan 2009 (https://sv.wikipedia.org/wiki/Lexbase).
Sajtens utgivare har stämts för förtal men friades i april i år av Stockholms tingsrätt. Frågan har 
lett till en översyn av grundlagen och en kommitté ska granska om skyddet för den personliga integriteten 
är tillräckligt stort på Lexbase och liknande databaser. Arbetet ska vara klart den 1 september 2016
(http://www.dn.se/ekonomi/mannen-bakom-skandalsajten-lexbase-frias/).
I USA finns en s.k. "mug shot publishing industry", där webbplatsägare publicerar "mug shots" från 
brottsbekämpande organs databaser och sedan tar ut en avgift för att ta bort någons foto från sajten, oavsett om 
personen visade sig vara skyldig eller inte (https://en.wikipedia.org/wiki/Mug_shot_publishing_industry).

2. Riktlinjer för utvecklare att tänka på om man vill vara "en god skrapare" mot serverägarna

Ta ansvar och vidta åtgärder om din webbskrapa orsakar problem.
Testa lokalt.
Undvik att lägga beslag på resurser.
Håll din webbskrapa under kontinuerlig uppsikt.
Respektera robots.txt samt eventuella andra anvisningar om att inte skrapa en webbplats.
Inkludera aktuell kontaktinformation (t.ex. en epost-adress) i "user agent"-fältet i request headern.  
(https://www.cs.washington.edu/lab/webcrawler-policy)
Man bör också studera de användarvillkor som publicerats på den webbplats man vill skrapa. I ett rättsfall 2010 mellan 
Ryanair Ltd och Billigfluege.de GmbH beslutade Irlands högsta domstol att Ryanairs klick-avtal är juridiskt bindande
(https://en.wikipedia.org/wiki/Web_scraping).

3. Begränsningar i min lösning - Generellt/Inte generellt

Länk till startsidan

Länken till startsidan hämtas från formuläret och sparas i en sessionsvariabel. Det går att byta ut länken till 
startsidan eftersom inga länkar i applikationen är hårdkodade.

Menylänkar

Jag räknar med att alla a-taggar på förstasidan är menylänkar, och att de presenteras i ordningen 
calendar, cinema, dinner. Jag räknar också med att man kan bygga ihop länkarna till de tre webbplatserna genom att 
lägga till menylänkarnas href-attribut efter länken till startsidan (i metoden getMenuRedirect i model.php). 
Jag hämtar dock ut url:en man blir omdirigerad till och tar bort eventuella snedstreck i slutet av länken och lägger 
sedan till ett igen, så applikationen borde fungera oavsett vilken url man blir omdirigerad till.

Calendar

Även här räknar jag med att alla a-taggar är länkar till vännernas kalendrar, och att länkarna kan byggas ihop genom 
att lägga till href-attributen efter länken till kalendrarnas startsida. Jag räknar också med att td-taggarna på 
vännernas kalender-sidor visar ok eller ej för endast fredag, lördag och söndag, i den ordningen. Man skulle dock kunna 
ändra ordningen på länkarna till vännernas kalendrar (peter.html etc) utan problem, och det spelar ingen roll om ordet
ok har skrivits med stora eller små bokstäver eller både och.

Cinema

Här räknar jag med att värdena för alternativen i den översta listrutan är 01, 02 och 03, och motsvarar fredag, lördag 
och söndag. Jag räknar också med att filmerna hämtas genom ett AJAX-anrop där värdena för dag och film anges som 
parametrar, och att en films value-attribut samt dess egenskap movie motsvarar varandra.

Dinner

Här räknar jag med att alla lediga bord ligger i en input i WordSection 2, 4 eller 6, beroende på om det är fredag, 
lördag eller söndag, och att ingen av filmerna är mer än 2 timmar långa. Jag räknar också med att value-attributet 
i en input (t.ex. lor1820) är det värde som ska skickas med för att boka ett bord, och att starttiden och sluttiden för
en bokning finns som karaktärer i detta attribut. URL:en som formuläret ska postas till byggs ihop genom att lägga till
formulärets action-attribut till startsidans url. Användarnamn och lösenord är hårdkodade men övriga namn och värden 
som ska skickas med vid en bokning hämtas ut från formuläret.

4. Robots.txt

Robots.txt-protokoll är ett sätt att be sökspindlar och andra robotar att inte besöka vissa delar av en webbplats. Rent 
praktiskt tillämpas metoden i form av en fil, "robots.txt", som placeras i webbplatsens rotkatalog (/). I filen anges 
regler för vilka kataloger eller sidor som inte skall besökas, och det är även möjligt att ge individuella regler för 
olika sökspindlar. Metoden bygger på samarbete från söktjänsternas och robotkodarnas sida – det finns 
inget krav på att sökspindlar måste följa konventionen (https://sv.wikipedia.org/wiki/Robots_Exclusion_Standard). 
Det är dock branschpraxis att respektera robots.txt, och om en webbskrapa skulle orsaka problem kan de serverägare som 
drabbats ha goda chanser att få rätt till skadestånd om de kan visa att webbskrapan inte respekterade deras robots.txt 
(https://www.cs.washington.edu/lab/webcrawler-policy).