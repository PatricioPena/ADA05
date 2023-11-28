<?php

//Ubicamos la ruta de la Api
define("EUROPEANAKEY", json_decode(file_get_contents("../Keys.json")));
define("RUTA", "https://api.europeana.eu/record/v2/search.json?wskey=");



class QueryHandler
{
    private $queryString;

    public function __construct($queryString)
    {
        $this->queryString = $queryString;
    }

//crea la consulta

    private function createQueryEP()
    {
        $completeQuery = "&query=";
        $String[] = strtok($this->queryString, " ");
        $tok = strtok(" ");
        while ($tok !== false) {
            $String[] = $tok;
            $tok = strtok(" ");
        }

//Para las consultas de String (varias palabras) realiza la
//busqueda utilizando el AND y realiza la query
//Consulta (Palabra1 AND Palabra2)
        foreach ($String as $k => $v) {
            if ($k < sizeof($String) - 1) {
                $completeQuery .= $v . "+AND+";
            } else {
                $completeQuery .= $v;
            }
        }
//retorna la consulta completada
        return $completeQuery;
    }

    //muestra la consulta en un nuevo documento, que en este caso será el Results
    //De una página nueva de html
    public function showQuery()
    {


        $htmlPage = new DOMDocument();
        $htmlPage->loadHTMLFile("../index.html");
        $resultsContent = $htmlPage->getElementById("results");
        $resultList = $htmlPage->createElement('ul');
        $queryS = $this->createQueryEP();
//key "hawlemono" fue otorgada por la Api para poder utilizar las busquedas
        $queryStr = http_build_query([
            'wskey' => "hawlemono",
            'query' => $queryS

        ]);
        $query = RUTA . "hawlemono" . $queryS;

        $results = file_get_contents($query);
        $obj = json_decode($results, true);

        //por cada item de la busqueda se va a crear en el html 
        //generamos un arreglo de estos items que son los resultados de la búsqueda
        foreach ($obj['items'] as $o) {
            $listItem = $htmlPage->createElement("li");
//Crea una condición si el item tiene un "score" que lo muestre y lo multiplique *.5 para quedar en rango 0-100
            if (isset($o['score'])) {
                //generamos el score o puntaje de la consulta
                $o["score"] = $o["score"] *.5 ;
                $listItem->textContent = $o["guid"] . " - SCORE: " . $o['score'];
            } else {
                $listItem->textContent = $o["guid"];
            }
        
            if ($listItem) {
                $resultList->appendChild($listItem);
            } else {
                echo ("error appending child to resultList");
            }
        }
        
        if ($resultList) {
            $resultsContent->appendChild($resultList);
        } else {
            echo ("error appending child to resultContent");
        }
        
        $htmlPage->saveHTMLFile("../indexResults.html");
        
        header("location: ../indexResults.html");
}}

?>