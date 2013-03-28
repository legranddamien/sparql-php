# SPARQL for PHP

A library that generate SPARQL queries and request a endpoints

## Exemple

A quick and easy sparql request to understand how it works. You can find more exemples in the SparqlTest.php file.

        $sparql = new Legrand\SPARQL;

        $sparql->varaible('?z')
               ->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '?y', '?z')
               ->limit(20);

        $response = $sparql->launch();

## Methods

- `prefixe($namespace, $url)` You can add several prefixes with this method
- `distinct($boolean)` Set a request with a distinct select
- `variable($var) Add a variable to select i.e. `?z`
- `where($subject, $predicate, $object)` add a where condition
- `optionalWhere($subject, $predicate, $object)` add a conditional where condition
- `union($sparql)` define where condition on a SPARQL object and give it to this method
- `filter($filter)` add a filter inside the where brackets
- `orderBy($order)` define the order i.e. `?z DESC`
- `limit($nb)` define the limit 
- `offset($nb)` define the offset

## Defaults
Some attributes are set with defaults value. You can of course change these values :

- `$sparql->baseUrl = 'http://dbpedia.org/sparql';`
- `$sparql->format = 'json';`
- `$sparql->method = 'GET';`
- `$sparql->queryParam = 'query';`
- `$sparql->formatParam = 'format';`