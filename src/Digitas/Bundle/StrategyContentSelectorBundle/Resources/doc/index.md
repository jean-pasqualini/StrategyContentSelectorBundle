#### Digitas Strategy Content Selector

Système de gestion de strategy de selection de contenu

Utilisation
===========

Dans un Controlleur

   $this->get("digitas_strategy_content_selector_bundle.id_strategy_selector_manager")
            ->getIdByStrategy(
                "auto",              // Nom de la strategy à utiliser
                true,                // Si on s'attend à recevoir un seul id ou plusieurs
                array([...]),        // Les ids qui peuvent être retourner (vide pour aucune restriction)
                array([...]),        // Les options pour personalisé la strategy utilisé (la configuration dépend de la stratégy utilisé)
                array([...]),        // Le contexte de decision (la configuration dépend de la stratégy utilisé)
            );

   Vous allez recevoir en retour soit une liste d'id ou un seul id en fonction du deuxième paramètres.

   Si aucun id n'est trouvée par la strategy elle vous renverra null

Cookbook
========

**Stratégy disponible**
- [Comment récuperer le premier elements d'un tableau ?](/src/Digitas/Bundle/StrategyContentSelectorBundle/Resources/doc/strategy-disponible/comment-recuperer-le-premier-element-dun-tableau.md)
- [Comment récuperer le ou les élements d'un tableau qui répond à des contraintes spécifique (validation symfony) ?](/src/Digitas/Bundle/StrategyContentSelectorBundle/Resources/doc/strategy-disponible/comment-recuperer-le-ou-les-elements-dun-tableau-qui-repond-a-des-contraintes-specifique.md)

Installation
============

Installer le avec le composer
    composer require digitas/strategy-content-selector-bundle

Activer le bundle dans votre application dans EzPublishKernel.php ou AppKernel.php

    new Digitas\Bundle\StrategyContentSelectorBundle\DigitasStrategyContentSelectorBundle(),

