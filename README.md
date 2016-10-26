# TwitchSlides
(readme en travaux)

## Twitter l'image
### Avec IFTTT.com
Mettre en place un recette (recipe) sur IFTTT.com qui relie la chaine Maker et envoie un tweet avec le message de votre choix, et une image définie comme {{Value1}}. Puis renseigner les variables $iftMakerKey et $iftRebusChannel dans le fichier "config.php".
### Avec l'API twitter
Il faut d'abord crééer une clef d'application twitter ainsi qu'un token à https://apps.twitter.com/app/new et configurer les paramètres adequat. Attention, ne pas oublier de définir $twitterUtiliserApi.

## Variables de configuration
Variables à définir dans le fichier *"config.php"* à la racine.

* **url_miniature_stream**: url de la miniature du stream, celle affichée par twitch dans la page "Suivis".
* **Nscreens**: nombre d'écrans configurables (chacun peut contenir une image).
  *Actuellement des valeurs différentes de 1 ne sont pas tellement testées.*
* **iftMakerKey**: l'identifiant de votre chaine Maker dans IFTTT.
* **iftRebusChannel**: le nom de l'event utilisé dans votre recette dans IFTTT.
* **twitterConsumerKey**: Consumer Key pour l'API twitter.
* **twitterConsumerSecret**: Consumer Secret pour l'API twitter.
* **twitterAccessToken**: Access Token pour l'API twitter.
* **twitterAccessTokenSecret**: Access Token Secret pour l'API twitter.
* **twitterUtiliserApi**: utiliser ou non l'API twitter.


## Fichier "prefs.json"
Le fichier prefs.json est géré par l'application, il ne faut pas le modifier à la main.
### Valeurs
* **tweets**: les 10 dernières urls d'images tweetés (pour empêcher les doublons)
* **screens**: liste des screens configurés indexés à partir de 1 (ouaip)
	* **file**: nom du fichier image actuel (ou "")
	* **top**: position en y
	* **left**: position en x
	* **zoom**: niveau de zoom de l'image (float)
* **strawpoll**: url du strawpoll (n'importe, on veut juste le gros nombre qu'on trouve dedans)
* **scoreboard_on**: true/false si on affiche les scores ou pas
* **scores**: scores indexés par le nom
	* **nom**: le nom (encore)
	* **score**: le score !
	* **t0**: time() de l'ajout du nom
	* **stamp**: time() de dernière modification

### Méthodes de la classe PrefsManager
* **screenFile($screenNum)**: renvoie le nom de fichier du screen au numéro demandé ou ""
* **screenPos($screenNum)**: renvoie [x,y,z] où (x,y) est la position est z le niveau de zoom
* **sortedScores()**: renvoie le tableau de scores, trié par score, puis dernière modification, puis nom
* **poll_embed()**: renvoie l'url d'embed du strawpoll (construite à partir du numéro)

