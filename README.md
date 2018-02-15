Introduction




Requirement

Requires symfony 4.2

Installation

Add the repository to your composer file.

"repositories": [
    { "type": "vcs", "url": "https://github.com/maalls/jmdictbundle" }
],


Run the composer command. 

composer require maalls/jmdict-bundle @dev 


Install the database.

php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate

Load the database.

php bin/console maalls:jmdict:load-database


Load the routing to have a peak of want can be done. (Requires mecab)

maalls_jmdict_bundle:
    # loads routes from the YAML or XML files found in some bundle directory
    resource: '@JMDictBundle/Controller/'
    type:     annotation
    prefix:   /jmdict


Example


Get english glossaries for a japanese word

// returns all the elements matching 世界
$words = $em->getRepository(\Maalls\JMDictBundle\Entity\Word::class)->findBy(["value" => "世界"]);

$word = $words[0];

// get all the glossaries related to an element.
$senseWords = $word->getSenseWords();
$senseWord = $senseWords[0];
$senseWord->getSense()->getSenseGlossaries();

