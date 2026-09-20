# Guide d'utilisation ATLost

## 1. Présentation de l'application

ATLost est une plateforme de signalement et de restitution de documents perdus ou retrouvés.

Elle permet de :
- signaler la présence d'un document trouvé,
- rechercher un document dans la base de données,
- demander la récupération d'un document auprès du déclarant,
- organiser un rendez-vous de restitution,
- payer la restitution selon le montant annoncé,
- vérifier le paiement côté administrateur,
- confirmer la restitution,
- créditer le signaleur avec son gain net,
- permettre un retrait depuis le solde interne de l'application.

L'application est structurée autour de deux profils principaux :
- Citoyen : déclare / recherche / réclame un document.
- Administrateur : valide les paiements, vérifie les demandes et gère l'usage global de la plateforme.

---

## 2. Rôles et accès

### 2.1 Profil citoyen
Un citoyen peut :
- créer un signalement de document trouvé,
- consulter sa liste de documents signalés,
- lancer une recherche de document,
- enregistrer des recherches sauvegardées,
- recevoir des notifications,
- faire une demande de récupération pour un document,
- fixer un rendez-vous,
- payer le montant de restitution,
- confirmer ou annuler une demande,
- consulter son solde de gains et retirer ses fonds disponibles.

### 2.2 Profil administrateur
Un administrateur peut :
- visualiser les rapports publics,
- vérifier les paiements déposés par les citoyens,
- accepter ou rejeter un paiement soumis,
- confirmer la restitution d'un document,
- gérer les rôles des utilisateurs via le tableau d'administration,
- superviser la plateforme.

> La gestion des rôles se fait via la table d'administration. La base de données contient aussi un compte administrateur configuré via variables d'environnement (`ADMIN_EMAIL`, `ADMIN_PASSWORD`) dans le seeder.

---

## 3. Prérequis pour lancer l'application

Avant de démarrer le projet, il faut avoir installé :
- PHP 8.4+
- Composer
- Node.js + npm
- une base de données compatible (SQLite par défaut dans ce projet)

### Commandes courantes

```bash
composer install
npm install
php artisan migrate
php artisan db:seed
php artisan serve
```

Pour le frontend à jour :

```bash
npm run dev
```

Ou en production / build :

```bash
npm run build
```

---

## 4. Création et accès au compte

### 4.1 Inscription
1. Ouvrir la page d'accueil.
2. Cliquer sur "S'inscrire" ou aller sur `/register`.
3. Saisir :
   - nom,
   - email,
   - mot de passe,
   - confirmation du mot de passe.
4. Valider.

L'utilisateur est automatiquement créé avec le rôle `citizen`.

### 4.2 Connexion
1. Aller sur `/login`.
2. Saisir l'email et le mot de passe.
3. Cliquer sur "Connexion".

Selon le rôle, l'utilisateur est redirigé vers :
- citoyen : tableau de bord citoyen,
- admin : tableau de bord administrateur.

### 4.3 Déconnexion
Le bouton de déconnexion est disponible dans le menu du dashboard.

---

## 5. Déclarer un document trouvé

### 5.1 Étapes
1. Se connecter en tant que citoyen.
2. Ouvrir le dashboard.
3. Aller dans la section "Déclarer un document" ou accéder au formulaire de signalement.
4. Remplir :
   - type de document (`CNI`, `Permis`, `Passeport`, `Autre`),
   - nom du propriétaire,
   - lieu,
   - téléphone,
   - récompense (montant de restitution),
   - description,
   - photo du document.
5. Valider le formulaire.

### 5.2 Effet du signalement
Le signalement est enregistré dans la base et affiché comme un document disponible. Les personnes correspondant à l'alerte peuvent recevoir une notification.

---

## 6. Rechercher un document

### 6.1 Recherche standard
Le citoyen peut :
- rechercher par nom,
- filtrer par type de document,
- parcourir les résultats récents,
- ouvrir la page détaillée d'un document.

### 6.2 Recherche sauvegardée
L'application permet aussi de sauvegarder une recherche et de configurer des alertes liées à certains critères.

Cette fonction est utile pour recevoir des notifications lorsqu'un document correspondant à des recherches enregistrées est ajouté.

---

## 7. Demande de récupération d'un document

Quand un citoyen pense être le propriétaire d'un document signalé :

1. Ouvrir le document concerné.
2. Cliquer sur "Demander la récupération".
3. La demande est envoyée au déclarant du document.

### 7.1 Statuts possibles
- `pending` : demande envoyée, en attente.
- `accepted` : déclarant accepte la demande.
- `cancelled` : demande annulée.
- `completed` : restitution confirmée.

---

## 8. Accepter une demande et organiser la restitution

Côté propriétaire du document :

1. Ouvrir la fiche du document.
2. Vérifier la demande reçue.
3. Cliquer sur "Accepter".
4. Proposer un rendez-vous.

### 8.1 Rendez-vous
Le rendez-vous doit être :
- défini dans le futur,
- enregistré dans le système.

Le demandeur et le déclarant reçoivent alors une notification à ce sujet.

---

## 9. Paiement de la restitution

Une fois la demande acceptée, le demandeur (celui qui veut récupérer le document) peut déclarer le paiement.

### 9.1 Formulaire de paiement
Le demandeur doit renseigner :
- mode de paiement : `mobile_money` ou `bank_transfer`,
- référence de paiement,
- justificatif si le virement bancaire est utilisé.

### 9.2 Montant payé
Le montant payé correspond à la récompense signalée pour le document.

### 9.3 Frais ATLost
Le système applique automatiquement un pourcentage de frais d'ATLost.

Par défaut :
- `ATLOST_SERVICE_FEE_PERCENTAGE = 10`

Exemple :
- récompense = 10 000 FCFA
- frais ATLost = 1 000 FCFA
- montant net remis au signaleur = 9 000 FCFA (après validation)

Les frais sont calculés dans le contrôleur de paiement et stockés dans `service_fee_amount`.

---

## 10. Vérification du paiement par l'admin

L'administrateur accède à la section "Paiements".

Il voit :
- nom du demandeur,
- document concerné,
- montant payé,
- frais ATLost,
- mode de paiement,
- référence,
- justificatif éventuel.

L'admin peut :
- valider le paiement,
- rejeter le paiement.

### 10.1 Quand le paiement est validé
- le statut devient `verified`,
- le demandeur est informé que la restitution peut être confirmée,
- le propriétaire du document peut ensuite confirmer la remise.

---

## 11. Confirmation de restitution

Quand le document est remis à la personne qui l'a demandé :

1. Le propriétaire du document ouvre la fiche concernée.
2. Il clique sur "Confirmer la restitution".
3. La restitution est validée.

### 11.1 Ce qui se passe alors
- le document est marqué comme `resolved`,
- le gain net est calculé,
- le montant est ajouté au solde du signaleur dans l'application,
- le gain est mis en état `available` pour retrait,
- des notifications sont générées pour les deux parties.

Le gain net est calculé comme ceci :

```text
gain_net = montant_payé - frais_ATLost
```

---

## 12. Système de portefeuille / retrait

ATLost garde l'argent dans un solde interne pour le signaleur, puis permet son retrait.

### 12.1 État du gain
Le gain peut être dans plusieurs états :
- `pending` : pas encore disponible,
- `available` : disponible pour retrait,
- `withdrawn` : le retrait a été effectué.

### 12.2 Retrait
Le signaleur peut retirer le gain disponible depuis son compte.

L'action :
- débite le solde interne,
- indique que le retrait est effectué,
- enregistre la date et l'amount retiré.

### 12.3 Règle métier importante
Le montant ne peut pas être retiré tant que la restitution n'a pas été confirmée.

Autrement dit :
- paiement validé → OK,
- document restitué vérifié → gain disponible,
- retrait ensuite possible.

---

## 13. Notifications

L'application envoie des notifications dans plusieurs situations :
- nouveau signalement enregistré,
- nouvelle demande de récupération,
- rendez-vous proposé,
- paiement à vérifier,
- paiement validé,
- document restitué,
- gain disponible,
- retrait effectué,
- demande annulée.

Les notifications sont visible dans le centre de notifications du dashboard.

---

## 14. Tableau de bord citoyen

Le tableau de bord citoyen affiche :
- nombre de documents signalés,
- documents restitués,
- documents en cours,
- recherches sauvegardées,
- notifications non lues,
- derniers rapports,
- progression globale.

---

## 15. Tableau de bord administrateur

Le tableau de bord admin présente :
- signalements en attente,
- signalements approuvés,
- documents restitués,
- nombre de citoyens,
- récapitulatif de l'activité,
- derniers documents traités.

La section "Paiements" permet de valider les paiements soumis.

---

## 16. Bonnes pratiques d'utilisation

### Pour un citoyen
- téléchargez une photo claire du document,
- renseignez un nom exact,
- indiquez un lieu précis,
- n'oubliez pas le montant de récompense,
- vérifiez les références de paiement avant validation,
- confirmez la restitution uniquement lorsque le document a bien été remis.

### Pour un administrateur
- vérifiez toujours les références et justificatifs,
- validez les paiements uniquement après contrôle,
- confirmez la restitution uniquement si les deux parties sont d'accord,
- surveillez les montants et les retraits.

---

## 17. Problèmes fréquents et vérifications

### 17.1 Erreur : page ne charge pas ou layout cassé
Vérifier que l'utilisateur est connecté avant d'appeler `auth()->user()->isAdmin()` dans les vues.

### 17.2 Paiement refusé
Vérifier :
- la référence de paiement,
- le mode de paiement,
- le justificatif pour un virement,
- le statut de la demande.

### 17.3 Demande annulée ou impossible à terminer
Vérifier si :
- le document est déjà restitué,
- la demande n'est pas annulée,
- le paiement a bien été validé,
- un rendez-vous a bien été fixé.

### 17.4 Gain non disponible au retrait
Vérifier que :
- la restitution a bien été confirmée,
- le statut `completed` est bien enregistré,
- le gain est en `available`.

---

## 18. Résumé du flux principal

```text
Citoyen signale un document trouvé
  ↓
Un autre citoyen cherche / voit le document
  ↓
Il demande la récupération
  ↓
Le déclarant accepte
  ↓
Le demandeur propose un rendez-vous
  ↓
Le demandeur déclare le paiement
  ↓
Admin vérifie le paiement
  ↓
Le document est restitué et confirmé
  ↓
Le signaleur reçoit son gain net dans le solde de l'application
  ↓
Le signaleur retire ses gains
```

---

## 19. Conclusion

ATLost fonctionne comme une plateforme de restitution sécurisée :
- elle centralise la déclaration,
- elle organise la récupération,
- elle sécurise le paiement,
- elle valide la restitution,
- elle met en place un système de gain et de retrait équitable avec commission ATLost.

C'est une application orientée vers la confiance, la traçabilité et la gestion de la restitution de documents sensibles.
