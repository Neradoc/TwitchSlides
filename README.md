# Twitch Slides
Twitch Slides est un logiciel en php et javascript qui permet à une personne de controler des images affichées sur un écran, ainsi qu'un strawpoll™ et une liste de scores. Il a été développé pour permettre à des personnes d'afficher des éléments dans un stream twitch à distance sans que le streamer n'aie besoin d'intervenir.

## Le fonctionnement de base
Sur un site situé à l'adresse http://site.web/
* Le streamer ajoute une capture de la page http://site.web/slide en plein écran, réglée pour la taille 1920x1080, et la dimensionne à la taille du stream (qu'on suppose donc en 16/9e).
* Le streamer peut également capturer http://site.web/strawpoll à l'endroit et la taille voulue, en établissant une couleur de transparence appropriée.
* L'assistant se connecte à http://site.web/gestion pour accéder au panneau de gestion.

### Le panneau de gestion
* En haut nous retrouvons les modules gérant ce qui est affiché sur le stream.
	* Le module "Écran" permet de choisir la taille et la position de l'image à l'écran, l'enlever et twitter (voir plus bas).
	* Le module "Strawpoll" permet d'affichage et masquage un strawpoll (les résultats).
	* Le module "Scores" permet de compter des scores qui seront affichés défilant en bas de l'écran sur le stream.
 * Le module "Ajouter une image" permet de mettre une image sur le site via le sélecteur de fichiers ou en mettant son adresse web directe.
 * Les modules d'images listent toutes les images disponibles et permettent de choisir la taille et la position d'une image avant de l'envoyer vers l'écran.

### Aller plus loin
La variable de configuration url_miniature_stream permet de déclarer l'adresse (relative ou absolue) de l'image de fond affichée dans le module écran (pas sur le stream), permettant d'aider au positionnement des images.

La variable de configuration Nscreens peut être augmentée (elle est à 1 par défaut) ajoutant autant de modules "écran" à la page de gestion. Chaque écran gère une image différente, qui seront toutes affichées en même temps dans le slide, les écrans de numéro plus élevé devant les autres.

Il est possible de n'afficher que certaines image à la fois en ajoutant le paramètre "screen" à l'adresse du slide, par exemple: http://site.web/slide?screen=1 ou http://site.web/slide?screen=2,5 par exemple.

N'oubliez pas qu'il est généralement possible (dans OBS par exemple) de limiter l'affichage d'une image à une région limitée de l'écran. Évidemment l'image miniature de fond ne correspondra plus. Vous pouvez également garder la taille de la zone en plein écran mais couper les bords ("crop") gardant ainsi la conrrespondance tout en controlant la zone affichée.

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

