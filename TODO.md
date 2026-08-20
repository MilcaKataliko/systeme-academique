# TODO — Amélioration du profil utilisateur avec photo

## Étapes
- [x] Explorer les fichiers pertinents (User, CustomAuthController, vues users/profil)
- [x] Valider le plan avec l'utilisateur
- [x] Créer la migration `add_photo_to_users`
- [x] Mettre à jour le modèle `User` ($fillable)
- [x] Mettre à jour `CustomAuthController@usersUpdate` pour gérer la photo
- [x] Mettre à jour `users/edit.blade.php` (champ photo + aperçu)
- [x] Mettre à jour `users/index.blade.php` (afficher la photo)
- [x] Mettre à jour `enseignant/profil.blade.php` (afficher la photo)
- [x] Mettre à jour `eleve/profil.blade.php` (afficher la photo)
- [x] Créer le dossier photos et le lien storage:link
- [x] Exécuter la migration

## Terminé
La fonctionnalité de photo de profil est entièrement implémentée.
