<?php

$cities = collect(json_decode(file_get_contents(base_path().'/resources/data/cities.json')));

return $cities;
