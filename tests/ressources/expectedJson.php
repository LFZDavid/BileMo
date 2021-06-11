<?php

$expectedJson = [

    "testProductListFirstPage" => 
    '{"items":[{"id":1,"name":"Apple-0","brand":"Apple","stock":234,"price":0,"link":{"self":"http:\/\/localhost\/api\/products\/1"}},{"id":2,"name":"Apple-1","brand":"Apple","stock":234,"price":12.34,"link":{"self":"http:\/\/localhost\/api\/products\/2"}},{"id":3,"name":"Apple-2","brand":"Apple","stock":234,"price":24.68,"link":{"self":"http:\/\/localhost\/api\/products\/3"}},{"id":4,"name":"Apple-3","brand":"Apple","stock":234,"price":37.02,"link":{"self":"http:\/\/localhost\/api\/products\/4"}},{"id":5,"name":"Apple-4","brand":"Apple","stock":234,"price":49.36,"link":{"self":"http:\/\/localhost\/api\/products\/5"}},{"id":6,"name":"Apple-5","brand":"Apple","stock":234,"price":61.7,"link":{"self":"http:\/\/localhost\/api\/products\/6"}},{"id":7,"name":"Apple-6","brand":"Apple","stock":234,"price":74.04,"link":{"self":"http:\/\/localhost\/api\/products\/7"}},{"id":8,"name":"Apple-7","brand":"Apple","stock":234,"price":86.38,"link":{"self":"http:\/\/localhost\/api\/products\/8"}},{"id":9,"name":"Samsung-0","brand":"Samsung","stock":234,"price":0,"link":{"self":"http:\/\/localhost\/api\/products\/9"}},{"id":10,"name":"Samsung-1","brand":"Samsung","stock":234,"price":12.34,"link":{"self":"http:\/\/localhost\/api\/products\/10"}}],"total":49,"count":10,"links":{"first":"\/api\/products?page=1","self":"\/api\/products?page=1","next":"\/api\/products?page=2","last":"\/api\/products?page=5"},"nbPages":5}',

    "testProductListSecondPage" =>
    '{"items":[{"id":11,"name":"Samsung-2","brand":"Samsung","stock":234,"price":24.68,"link":{"self":"http:\/\/localhost\/api\/products\/11"}},{"id":12,"name":"Samsung-3","brand":"Samsung","stock":234,"price":37.02,"link":{"self":"http:\/\/localhost\/api\/products\/12"}},{"id":13,"name":"Samsung-4","brand":"Samsung","stock":234,"price":49.36,"link":{"self":"http:\/\/localhost\/api\/products\/13"}},{"id":14,"name":"Samsung-5","brand":"Samsung","stock":234,"price":61.7,"link":{"self":"http:\/\/localhost\/api\/products\/14"}},{"id":15,"name":"Samsung-6","brand":"Samsung","stock":234,"price":74.04,"link":{"self":"http:\/\/localhost\/api\/products\/15"}},{"id":16,"name":"Samsung-7","brand":"Samsung","stock":234,"price":86.38,"link":{"self":"http:\/\/localhost\/api\/products\/16"}},{"id":17,"name":"Huawei-0","brand":"Huawei","stock":234,"price":0,"link":{"self":"http:\/\/localhost\/api\/products\/17"}},{"id":18,"name":"Huawei-1","brand":"Huawei","stock":234,"price":12.34,"link":{"self":"http:\/\/localhost\/api\/products\/18"}},{"id":19,"name":"Huawei-2","brand":"Huawei","stock":234,"price":24.68,"link":{"self":"http:\/\/localhost\/api\/products\/19"}},{"id":20,"name":"Huawei-3","brand":"Huawei","stock":234,"price":37.02,"link":{"self":"http:\/\/localhost\/api\/products\/20"}}],"total":49,"count":10,"links":{"first":"\/api\/products?page=1","prev":"\/api\/products?page=1","self":"\/api\/products?page=2","next":"\/api\/products?page=3","last":"\/api\/products?page=5"},"nbPages":5}',

    "testProductListWithBrandFilter" => '{"items":[{"id":1,"name":"Apple-0","brand":"Apple","stock":234,"price":0,"link":{"self":"http:\/\/localhost\/api\/products\/1"}},{"id":2,"name":"Apple-1","brand":"Apple","stock":234,"price":12.34,"link":{"self":"http:\/\/localhost\/api\/products\/2"}},{"id":3,"name":"Apple-2","brand":"Apple","stock":234,"price":24.68,"link":{"self":"http:\/\/localhost\/api\/products\/3"}},{"id":4,"name":"Apple-3","brand":"Apple","stock":234,"price":37.02,"link":{"self":"http:\/\/localhost\/api\/products\/4"}},{"id":5,"name":"Apple-4","brand":"Apple","stock":234,"price":49.36,"link":{"self":"http:\/\/localhost\/api\/products\/5"}},{"id":6,"name":"Apple-5","brand":"Apple","stock":234,"price":61.7,"link":{"self":"http:\/\/localhost\/api\/products\/6"}},{"id":7,"name":"Apple-6","brand":"Apple","stock":234,"price":74.04,"link":{"self":"http:\/\/localhost\/api\/products\/7"}},{"id":8,"name":"Apple-7","brand":"Apple","stock":234,"price":86.38,"link":{"self":"http:\/\/localhost\/api\/products\/8"}}],"total":8,"count":8,"links":{"first":"\/api\/products?page=1","self":"\/api\/products?page=1","last":"\/api\/products?page=1"},"nbPages":1}',

    "testWrongProductListPaginator" => '{"status":404,"type":"about:blank","title":"Not Found"}',

    "testProductShow" => '{"id":49,"name":"find","brand":"Test","stock":0,"price":0,"link":{"self":"http:\/\/localhost\/api\/products\/49"}}',

    "testWrongProductShow" => '{"status":404,"type":"about:blank","title":"Not Found"}',

    "testCustomerList" => '{"items":[{"id":301,"name":"find","link":{"self":"http:\/\/localhost\/api\/customers\/301"}},{"id":302,"name":"already_exist","link":{"self":"http:\/\/localhost\/api\/customers\/302"}},{"id":303,"name":"delete","link":{"self":"http:\/\/localhost\/api\/customers\/303"}}],"total":3,"count":3,"links":{"first":"\/api\/customers?page=1","self":"\/api\/customers?page=1","last":"\/api\/customers?page=1"},"nbPages":1}',

    "testCustomerListFirstPage" => '{"items":[{"id":301,"name":"find","link":{"self":"http:\/\/localhost\/api\/customers\/301"}},{"id":302,"name":"already_exist","link":{"self":"http:\/\/localhost\/api\/customers\/302"}},{"id":303,"name":"delete","link":{"self":"http:\/\/localhost\/api\/customers\/303"}}],"total":3,"count":3,"links":{"first":"\/api\/customers?page=1","self":"\/api\/customers?page=1","last":"\/api\/customers?page=1"},"nbPages":1}',

    "testWrongCustomerListPaginator" => '{"status":404,"type":"about:blank","title":"Not Found"}',
    
    "testCustomerShow" => '{"id":301,"name":"find","link":{"self":"http:\/\/localhost\/api\/customers\/301"}}',

    "testCustomersWithFilterName" =>'{"items":[{"id":301,"name":"find","link":{"self":"http:\/\/localhost\/api\/customers\/301"}}],"total":1,"count":1,"links":{"first":"\/api\/customers?page=1","self":"\/api\/customers?page=1","last":"\/api\/customers?page=1"},"nbPages":1}',

    "testWrongCustomerShow" => '{"status":404,"type":"about:blank","title":"Not Found"}',

    "testCreateCustomer" => '{"id":305,"name":"new Customer Test","link":{"self":"http:\/\/localhost\/api\/customers\/305"}}',

    "testCreateCustomerWithoutData" => '{"status":400,"type":"invalid_body_format","title":"Invalid JSON format sent"}',

    "testCreateCustomerWithBlankName" => '{"status":400,"type":"invalid_body_format","title":"Invalid JSON format sent"}',

    "testCreateCustomerNameAlreadyExist" => '{"errors":{"name":"Un client existe d\u00e9j\u00e0 \u00e0 ce nom."},"status":400,"type":"validation_error","title":"There was a validation error"}',
    
    "testDeleteCustomer" => '',

    "testDeleteOtherSuppliersCustomer" => '{"detail":"Vous n\u0027\u00eates pas authoris\u00e9 \u00e0 supprimer ce client!","status":403,"type":"about:blank","title":"Forbidden"}',
    
    "testWrongCustomerDelete" => '{"status":404,"type":"about:blank","title":"Not Found"}',

    '404' => '{"status":404,"type":"about:blank","title":"Not Found"}',
];

define('EXPECTED_JSON', $expectedJson);