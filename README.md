# Requirement

Requires symfony 4.2.

# Installation

Add the repository to your composer.json file.

```
"repositories": [
    { "type": "vcs", "url": "https://github.com/maalls/jmdictbundle" }
],
```

Run the composer command. 
```bash
composer require maalls/jmdict-bundle @dev 
```

Install the database.
```
php bin/console doctrine:migrations:diff
```
```
php bin/console doctrine:migrations:migrate
```

Load the database.

```
php bin/console maalls:jmdict:load-database
```

If you have mecab install, add the following in config/routing.yml then go to /jmdict/search to have a peak of want can be done. 
```
maalls_jmdict_bundle:
    # loads routes from the YAML or XML files found in some bundle directory
    resource: '@JMDictBundle/Controller/'
    type:     annotation
    prefix:   /jmdict
```


# Example


Get english glossaries for a japanese word

```php
// returns all the elements matching 世界
$words = $em->getRepository(\Maalls\JMDictBundle\Entity\Word::class)->findBy(["value" => "世界"]);

$word = $words[0];

// get all the glossaries related to an element.
$senseWords = $word->getSenseWords();
$senseWord = $senseWords[0];
$senseWord->getSense()->getSenseGlossaries();
```
