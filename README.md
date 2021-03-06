# Twitch Slides
Twitch Slides est un logiciel en php et javascript qui permet à une personne de controler des images affichées sur un écran, ainsi qu'un strawpoll™ et une liste de scores. Il a été développé pour permettre à des personnes d'afficher des éléments dans un stream twitch à distance sans que le streamer n'aie besoin d'intervenir.

## Le fonctionnement de base
Sur un site situé à l'adresse http://site.web/
* Le streamer ajoute une capture de la page http://site.web/slide en plein écran, réglée pour la taille 1920x1080, et la dimensionne à la taille du stream (qu'on suppose donc en 16/9e).
* Le streamer peut également capturer http://site.web/strawpoll à l'endroit et la taille voulue, en établissant une couleur de transparence appropriée.
* L'assistant se connecte à http://site.web/gestion pour accéder au panneau de gestion.
* Certains paramètres de l'interface de gestion peuvent être modifiés dans le panneau de configuration à http://site.web/config

### Le panneau de gestion
* En haut nous retrouvons les modules gérant ce qui est affiché sur le stream.
	* Les modules "Image" permettent de positionner une image à l'écran, l'enlever et twitter.
	* Le module "Strawpoll" permet d'affichage et masquage un strawpoll (les résultats).
	* Le module "Scores" permet de compter des scores qui seront affichés défilant en bas de l'écran sur le stream.
* Le module "Ajouter une image" permet de mettre une image sur le site via le sélecteur de fichiers ou en mettant son adresse web directe.
* Les modules d'images "Sources" listent toutes les images disponibles et permettent de choisir la taille et la position d'une image avant de l'envoyer vers l'écran.

#### Plus en détails
* Le bouton ON/OFF permet de masquer un module dans le stream sans perdre ses réglages.
* Module d'images et modules de sources:
	* Déplacer l'image en la glissant à la souris. La touche shift maintient l'image dans l'écran.
	* Redimensionner l'image en maintenant la touche alt en glissant à la souris.
	* Positionner l'image avec les boutons "@" apparaissant quand on survole l'image.
	* Changer l'échelle de l'image avec les boutons "+", "-" et "=".
	* Déplacer l'image pas à pas avec les boutons flèches.
* Module d'images:
	* Les images sont dans l'ordre du fond vers l'avant (la dernière est devant).
	* Les boutons flèches déplacent les images vers l'avant ou l'arrière.
	* Le bouton "Changer" valide les changements de position et dimension.
	* Le bouton "Effacer" retire l'image de l'écran.
	* Le bouton "Twitter" appelle la fenêtre twitter.
	* L'indicateur de timer affiche le temps ou le score et permet de le modifier.
* Module de sources:
	* Le bouton "Effacer" supprime l'image du serveur.
	* Le menu "Ajouter" ajoute l'image en l'insérant à la position demandée.
	* Le menu "Remplacer" remplace une image en conservant le timer.
	* Le bouton ![Étoile](cjs/img/star-mini.png) Étoile sous l'image permet de la marquer favorite.
	* Si il y a plus de 12 images, la liste des pages apparait au dessus des images.
	* Le bouton (![Étoile](cjs/img/star-mini.png)) Étoile dans la liste des pages accède aux favoris.
* Module strawpoll:
	* Entrer l'url du strawpoll (seul le numéro compte) et valider avec entrée. La page de résultats (en histogrammes) sera affichée.
	* Le bouton "Effacer" efface l'url du strawpoll.
* Module scores:
	* Le menu de position permet de décider si les scores s'affichent devant ou derrière les images.
	* Ajouter un joueur avec le champ texte en bas et son score (1 par défaut). Valider avec entrée ou avec le bouton coche.
	* Le champ de texte permet aussi de filtrer les noms (pour voir si le joueur est déjà là par exemple).
	* Les boutons "+" et "-" changent le nombre de points de la valeur indiquée au milieu (pour ne pas avoir à faire des additions de tête).
	* Le champ contenant le score est modifiable pour donner directement un score. Valider avec entrée.
	* La croix rouge retire le joueur.
	* Un bouton "EFFACER LES SCORES" dans la page de config permet de supprimer tous les scores.
	

### Aller plus loin
La page de config (accessible depuis un onglet dans la page de gestion) permet de changer certains paramètres pour personnaliser l'interface de gestion.

La configuration de **l'image de fond des écrans** permet de déclarer l'adresse (relative ou absolue) de l'image de fond affichée dans le module écran (pas sur le stream), permettant d'aider au positionnement des images.

Il est possible d'afficher l'image de fond sur le "slide" en ajoutant la variable debug à l'adresse, pour prévisualiser l'ensemble des images ensemble (à ne pas faire sur le stream par contre, ça cacherait tout).

N'oubliez pas qu'il est généralement possible (dans OBS par exemple) de limiter l'affichage d'une image à une région limitée de l'écran. Évidemment l'image miniature de fond ne correspondra plus. Vous pouvez également garder la taille de la zone en plein écran mais couper les bords ("crop") gardant ainsi la correspondance tout en controlant la zone affichée. Avec OBS alt-click permet de croper à la souris.

## Liste des scores
Les scores sont positionnés par rapport aux images (devant/derrière) selon le paramètre du menu correspondant dans le module de scores. Quand des images sont ajoutées ou retirées, le logiciel tente de recalculer la position des scores en conséquence.

Sous chaque image, un champ indique le score de l'image. Si la configuration "calc_score" n'est pas présente, c'est le nombre de minutes depuis que l'image a été ajoutée. Si "calc_score" est défini et ne provoque pas d'erreur, c'est le score calculé qui est affiché, avec une icone de pièce.

Le calcul du score est une expression mathématique (en javascript) qui doit retourner un nombre entier. La variable "time" est disponible et contient le nombre de minutes depuis que l'image a été ajoutée sur le stream (qu'elle soit ON ou OFF). Exemple: *calc_score = "1 + time / 2"*.

On peut modifier le timer en rentrant un nombre de minutes et valider avec la touche entrée. Attention il n'y a pas de calcul inverse, c'est toujours le nombre de minutes et non le score qu'on entre dans ce champ. Si un score est affiché, le nombre de minutes est visible dans la bulle d'informations affichée en mettant la souris sur le timer.

## Twitter l'image
### Avec IFTTT.com
Mettre en place un recette (recipe) sur IFTTT.com qui relie la chaine Maker et envoie un tweet avec le message défini comme {{Value2}}, et une image définie comme {{Value1}}. Puis renseigner les variables twitterIftMakerKey et twitterIftChannel dans le fichier "config.ini".
### Avec l'API twitter
Il faut d'abord crééer une clef d'application twitter ainsi qu'un token à https://apps.twitter.com/app/new et configurer les paramètres adequat dans le fichier "config.ini". Attention, ne pas oublier de définir twitterUtiliserApi.

Les messages par défaut proposés dans l'interface de twitter peuvent être modifiés, retirés, ajoutés, dans l'interface de configuration.

## Variables de configuration
Variables à définir dans le fichier *"data/config.ini"*. Le format exact est indiqué dans le fichier *"config.exemple.ini"* distribué avec l'application. Certaines de ces valeurs servent de valeur par défaut et sont replacées par celles de l'interface de configuration.

* **htmlTitleGestion**: titre de la page gestion (au sens de la balise title).
* **url_miniature_stream**: url de la miniature du stream, celle affichée par twitch dans la page "Suivis".
* **calc_score**: formule de calcul du score, en fonction de "time" (nombre de minutes).
* Twitter par If-This-Then-That
	* **twitterIftMakerKey**: l'identifiant de votre chaine Maker dans IFTTT.
	* **twitterIftChannel**: le nom de l'event utilisé dans votre recette dans IFTTT.
* Twitter par l'API
	* **twitterUtiliserApi**: utiliser ou non l'API twitter.
	* **twitterConsumerKey**: Consumer Key pour l'API twitter.
	* **twitterConsumerSecret**: Consumer Secret pour l'API twitter.
	* **twitterAccessToken**: Access Token pour l'API twitter.
	* **twitterAccessTokenSecret**: Access Token Secret pour l'API twitter.
* **twitterMessages[]**: liste des messages pré-configurés dans l'interface twitter.
  Mettez en autant que vous voulez, ils seront ajoutés à la liste.

# Information pour les développeurs
## Notes diverses
La page *slide* interroge le site toutes les secondes pour connaitre les informations à afficher et met à jour les parties de la page qui doivent l'être. La page n'est pas rechargée en entier pour éviter un éventuel clignotement.

## Fichier "prefs.json"
Le fichier prefs.json est géré par l'application, il ne faut pas le modifier à la main.
### Valeurs
* **screens**: liste des screens configurés indexés à partir de 1 (ouaip).
	* **file**: nom du fichier image actuel (ou "").
	* **top**: position en y.
	* **left**: position en x.
	* **zoom**: niveau de zoom de l'image (float).
	* **on**: si l'image doit être affichée ou non.
* **tweets**: les 10 dernières urls d'images tweetés (pour empêcher les doublons).
* **strawpoll_on**: true/false si on affiche le strawpoll ou pas.
* **strawpoll**: url du strawpoll (n'importe, on veut juste le gros nombre qu'on trouve dedans).
* **scoreboard_on**: true/false si on affiche les scores ou pas.
* **scores**: scores indexés par le nom.
	* **nom**: le nom (encore).
	* **score**: le score !
	* **t0**: time() de l'ajout du nom.
	* **stamp**: time() de dernière modification.
* **stars**: liste des images favorites comme clefs. La valeur (actuellement true/false) sera peut-être utilisée plus tard pour ranger les images sources dans plusieurs catégories.
* **reload_slide**: force le rechargement de la page *slide* toute entière (n'a pas d'interface).

### Méthodes de la classe PrefsManager
* **screenFile($screenNum)**: renvoie le nom de fichier du screen au numéro demandé ou "".
* **screenPos($screenNum)**: renvoie [x,y,z] où (x,y) est la position est z le niveau de zoom.
* **screenOn($screenNum)**: envoie true/false selon que l'cran est activé ou non.
* **effacer_screen($screen)**: vide un écran et efface le fichier image associé si il n'est pas dans un autre écran.
* **sortedScores()**: renvoie le tableau de scores, trié par score, puis dernière modification, puis nom.
* **poll_embed()**: renvoie l'url d'embed du strawpoll (construite à partir du numéro)
* **to be continued**
