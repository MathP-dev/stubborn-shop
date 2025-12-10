# Styles et Scripts dans le projet Stubborn

## Règle générale
**Aucun style ou script inline n'est utilisé dans le projet**, sauf exception documentée ci-dessous.

## Structure des fichiers

### CSS
- `public/css/main.css` - Styles principaux de l'application
- `public/css/email.css` - Documentation des styles email (non utilisé directement)

### JavaScript
- `public/js/main.js` - Scripts principaux de l'application
  - Gestion des fallbacks d'images
  - Confirmations de formulaires
  - Autres interactions

## Exception : Templates d'emails

Les templates suivants utilisent des **styles inline** car c'est une **exigence technique** :
- `templates/emails/verification.html.twig`
- `templates/emails/order_confirmation.html.twig`

**Raison** : La plupart des clients email (Gmail, Outlook, etc.) ne supportent pas les feuilles de style externes ni les balises `<style>` dans le `<head>`. Les styles doivent donc être inline pour garantir un rendu correct.

## Classes CSS personnalisées

### Navigation
- `.navbar-brand img` - Logo dans la navbar
- `.active-link` - Lien actif dans le menu

### Produits
- `.featured-products` - Section des produits en vedette
- `.product-card` - Carte produit avec animation hover
- `.product-card img` - Image produit dans la carte

### Panier
- `.cart-product-img` - Image miniature dans le panier (80px)

### Admin
- `.admin-product-preview` - Aperçu du produit (max 200px)
- `.admin-product-thumb` - Vignette du produit (60x60px)

### Paiement
- `.payment-card` - Carte de confirmation de paiement (max 500px)

## Classes pour JavaScript

### Images avec fallback
Utiliser l'attribut `data-fallback` sur les images :
```html
<img src="..." data-fallback="https://via.placeholder.com/...">
```

### Formulaires avec confirmation
- `.cart-remove-form` - Formulaire de retrait d'article du panier
- `.product-delete-form` - Formulaire de suppression de produit (admin)

## Bootstrap
Le projet utilise Bootstrap 5.3.0 pour la majorité du styling et des composants.
Les classes Bootstrap sont privilégiées avant de créer des styles personnalisés.

