#### Digitas Strategy Content Selector

Système de gestion de strategy de selection de contenu :

Example :

            $this->idSelectorManager->getIdByStrategy(
                // Strategy
                $strategyConfiguration["strategy"],
                // One id attemps (si un seul résultat est attendu)
                true,
                // Ids availables
                array([...]),
                // Options strategy
                array(
                    [...]
                ),
                // Context
                array(
                    [...]
                )
            );