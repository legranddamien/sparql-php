<?php

class SparqlTest extends PHPUnit_Framework_TestCase
{
    public function testBasicQuery()
    {
        $sparql = new Legrand\SPARQL;

        $sparql->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '?x', '?y');

        $expected = "SELECT * WHERE { <http://dbpedia.org/resource/Nine_Inch_Nails> ?x ?y }";

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with juste one where');
    }

    public function testBasicQueryWithLimit()
    {
        $sparql = new Legrand\SPARQL;

        $sparql->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '?x', '?y')->limit(10);

        $expected = "SELECT * WHERE { <http://dbpedia.org/resource/Nine_Inch_Nails> ?x ?y } LIMIT 10";

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with juste one where and a limit of 10');
    }

    public function testBasicQueryWithFroms()
    {
        $sparql = new Legrand\SPARQL;

        $sparql->from('http://graph1')->from('http://graph2')->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '?x', '?y');

        $expected = "SELECT * FROM <http://graph1> FROM <http://graph2> WHERE { <http://dbpedia.org/resource/Nine_Inch_Nails> ?x ?y }";

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with two from');
    }

    public function testBasicQueryWithLimitAndOffset()
    {
        $sparql = new Legrand\SPARQL;

        $sparql->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '?x', '?y')->limit(10)->offset(10);

        $expected = "SELECT * WHERE { <http://dbpedia.org/resource/Nine_Inch_Nails> ?x ?y } LIMIT 10 OFFSET 10";

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with juste one where and a limit of 10, offset of 10');
    }

    public function testBasicQueryWithVariable()
    {
        $sparql = new Legrand\SPARQL;

        $sparql->variable('?y')->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '?x', '?y');

        $expected = "SELECT ?y WHERE { <http://dbpedia.org/resource/Nine_Inch_Nails> ?x ?y }";

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with juste one where and select a variable');
    }

    public function testBasicQueryWithVariableAndDistinct()
    {
        $sparql = new Legrand\SPARQL;

        $sparql->distinct(true)->variable('?y')->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '?x', '?y');

        $expected = "SELECT DISTINCT ?y WHERE { <http://dbpedia.org/resource/Nine_Inch_Nails> ?x ?y }";

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with juste one where and select a distinct variable');
    }

    public function testBasicQueryWithVariables()
    {
        $sparql = new Legrand\SPARQL;

        $sparql->variable('?x')->variable('?y')->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '?x', '?y');

        $expected = "SELECT ?x ?y WHERE { <http://dbpedia.org/resource/Nine_Inch_Nails> ?x ?y }";

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with juste one where and select two variables');
    }

    public function testBasicQueryWithFilter()
    {
        $sparql = new Legrand\SPARQL;

        $sparql->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '?x', '?y')->filter('?x = rdfs:comment');

        $expected = "SELECT * WHERE { <http://dbpedia.org/resource/Nine_Inch_Nails> ?x ?y . FILTER (?x = rdfs:comment) }";

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with a filter');
    }

    public function testBasicQueryWithMultipleWhere()
    {
        $sparql = new Legrand\SPARQL;

        $sparql->variable('?label')->where('<http://dbpedia.org/resource/Nine_Inch_Nails>', '<http://dbpedia.org/ontology/associatedBand>', '?y')->where('?y', 'rdfs:label', '?label');

        $expected = "SELECT ?label WHERE { <http://dbpedia.org/resource/Nine_Inch_Nails> <http://dbpedia.org/ontology/associatedBand> ?y . ?y rdfs:label ?label }";

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with a filter');
    }

    public function testComplexQueryWithUnion()
    {
        $sparql = new Legrand\SPARQL;
        $sparql2 = new Legrand\SPARQL;

        $sparql2->where('?uri', 'rdf:label', '?label')->filter('lang(?label) = "en" && regex(?label, "Nine Inch Nails")');

        $sparql->variable('?uri')->where('?uri', 'rdf:label', '?label')->filter('lang(?label) = "en" && regex(?label, "Metallica")')->union($sparql2);

        $expected = 'SELECT ?uri WHERE { { ?uri rdf:label ?label . FILTER (lang(?label) = "en" && regex(?label, "Metallica")) }UNION { ?uri rdf:label ?label . FILTER (lang(?label) = "en" && regex(?label, "Nine Inch Nails")) } }';

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with a filter');
    }

    public function testDelete()
    {
        $sparql = new Legrand\SPARQL;
        $sparql->delete('http://graph', '<http://element/id> ?x ?y')->where('<http://element/id>', '?x', '?y');

        $expected = 'DELETE FROM <http://graph> { <http://element/id> ?x ?y } WHERE { <http://element/id> ?x ?y }';

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with a filter');
    }

    public function testSelectInGraph()
    {
        $sparql = new Legrand\SPARQL;
        $sparql->select('http://graph')->where('<http://element/id>', '?x', '?y');

        $expected = 'SELECT * WHERE { GRAPH <http://graph> { <http://element/id> ?x ?y } }';

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with a filter');
    }

    public function testSelectInGraphWithGroupBy()
    {
        $sparql = new Legrand\SPARQL;
        $sparql->select('http://graph')->where('<http://element/id>', '?x', '?y')->groupBy('?x');

        $expected = 'SELECT * WHERE { GRAPH <http://graph> { <http://element/id> ?x ?y } } GROUP BY ?x';

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with a filter');
    }

    public function testPrefixes()
    {
        $sparql = new Legrand\SPARQL;
        $sparql->prefixe('foaf', 'http://xmlns.com/foaf/0.1/')->select('http://graph')->where('<http://element/id>', '?x', '?y')->groupBy('?x');

        $expected = 'PREFIX foaf: <http://xmlns.com/foaf/0.1/>  SELECT * WHERE { GRAPH <http://graph> { <http://element/id> ?x ?y } } GROUP BY ?x';

        $actual = $this->cleanQuery($sparql->getQuery());

        $this->assertEquals($expected, $actual, 'The simple query with prefixes');
    }
















    private function cleanQuery($query)
    {
        return trim(str_replace("\n", " ", $query));
    }
}