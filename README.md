# Réserva'Classe : Réservation de créneaux pour les réunions parents-enseignants

![cover](screenshot.webp)

Consulter sur : <http://sc4foal9574.universe.wf/reservaclasse/>

Application Web de gestion de créneaux horaires : un projet perso. pour découvrir le framework PHP Symfony.
La version 1.0 est volontairement simple, elle est destinée à évoluer.

# Spécificités (V1.0)

* 1 seul Enseignant
* 1 page "Réservations" pour visualiser tous les rendez-vous validés.
* 1 page "Réserver un créneau" pour voir/réserver un créneau disponible parmi les jours autorisés.
* La liste des élèves est insérée dans le champs \<select> de la Modal de réservation.
* Chaque créneau est défini par une Date et heure de Début. L'heure de fin est calculée en ajoutant 20 minutes.
* Pour le moment, les données (liste des élèves et créneaux date/heure proposés) sont insérées manuellement en BDD.
* Chaque User (l'élève) peut réserver plusieurs créneaux (utile pour conserver l'historique de ces RdV dans le cas de la création d'un Espace utilisateur par la suite).

## Roadmap

* Ajout d'un LOGIN pour l'enseignant.
* L'enseignant pourra modifier le statut de la réservation (de 'En attente' à 'confirmée') et supprimer une réservation depuis son Espace.
* Création d'un CRUD pour gérer les créneaux horaires (avec définition de la durée du RdV) depuis son Espace.
