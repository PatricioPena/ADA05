<?php
define("EUROPEANAKEY", json_decode(file_get_contents("../Keys.json")));
define("RUTA", "https://api.europeana.eu/record/v2/search.json?wskey=");

class QueryHandler
{

    private $queryString;

    public function __construct($queryString)
    {
        $this->queryString = $queryString;
    }

    private function createQueryEP()
    {
        $completeQuery = "&query=";
        $String[] = strtok($this->queryString, " ");
        $tok = strtok(" ");
        while ($tok !== false) {
            $String[] = $tok;
            $tok = strtok(" ");
        }


        foreach ($String as $k => $v) {
            if ($k < sizeof($String) - 1) {
                $completeQuery .= $v . "+AND+";
            } else {
                $completeQuery .= $v;
            }
        }

        return $completeQuery;
    }

    public function showQuery()
    {


        $htmlPage = new DOMDocument();
        $htmlPage->loadHTMLFile("../index.html");
        $resultsContent = $htmlPage->getElementById("results");
        $resultList = $htmlPage->createElement('ul');
        $queryS = $this->createQueryEP();

        $queryStr = http_build_query([
            'wskey' => "hawlemono",
            'query' => $queryS

        ]);
        $query = RUTA . "hawlemono" . $queryS;

        $results = file_get_contents($query);
        $obj = json_decode($results, true);

        foreach ($obj['items'] as $o) {
            // print_r($o['guid']);
            // echo ("</br>");
            $listItem = $htmlPage->createElement("li");
            $listItem->textContent = $o["guid"];

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


    }
}

?>