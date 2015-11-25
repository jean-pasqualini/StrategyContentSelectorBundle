# Comment récuperer le ou les élements d'un tableau qui répond à des contraintes spécifique (validation symfony)

Cette stratégy s'appelle *"validation_symfony"*.

Elle selectionne les contraintes du tableau d'options qui matche avec les données passé dans le contexte et retourne la valeur associé à la première contriante valider.

Elle prends les options suivantes :

- aiguilleurs[]
    - 0
       - constraints:
           - block0.test:
              - GreaterThan: { value : 10 }
              - LessThan: { value: 15 }
       - value: 3
    - [...]

Elle prends le contexte suivant :

- data
    - block0[]
        - test : 4
    - [...]

data est le tableau de données à valdier


