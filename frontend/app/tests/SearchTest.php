<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use NavetSearch\Helper\Config;
use NavetSearch\Helper\Request;
use NavetSearch\Helper\Response;
use NavetSearch\Helper\Search;
use NavetSearch\Interfaces\AbstractSession;

final class SearchTest extends TestCase
{
    private $person = <<<END
    {
        "givenName":"givenName",
        "familyName":"familyName",
        "address":{
            "@type":"FOLKBOKFORINGSADRESS",
            "addressLocality":"addressLocality",
            "postalCode":"25450",
            "streetAddress":"streetAddress",
            "provinceCode":"12",
            "municipalityCode":"83"
        }
    }
    END;

    private $relations = <<<END
    {
        "civilStatus":
            {
                "code":"G",
                "description":"Gift",
                "date":"20000101"
            },
            "relationsToFolkbokforda":[{
                "identityNumber":"2000101010101",
                "type":{
                    "code":"VF",
                    "description":"Vårdnadshavare för"
                },
                "custodyDate":"20000101",
                "deregistration":null
            },{
                "identityNumber":"2000101010101",
                "type":{
                    "code":"VF",
                    "description":"Vårdnadshavare för"
                },
                "custodyDate":"20120201",
                "deregistration":null
            },{
                "identityNumber":"2000101010101",
                "type":{
                    "code":"B",
                    "description":"Barn"
                },
                "custodyDate":"",
                "deregistration":null
            },{
                "identityNumber":"2000101010101",
                "type":{
                    "code":"B",
                    "description":"Barn"
                },
                "custodyDate":"",
                "deregistration":null
            },{
                "identityNumber":"2000101010101",
                "type":{
                    "code":"M","description":"Make/maka"
                },
                "custodyDate":"",
                "deregistration":null
            },{
                "identityNumber":"2000101010101",
                "type":{"code":"MO","description":"Mor"},
                "custodyDate":"",
                "deregistration":null
            },{
                "identityNumber":"2000101010101",
                "type":{
                    "code":"FA",
                    "description":"Far"},
                    "custodyDate":"",
                    "deregistration":null
                }],
                "relationsToAldrigFolkbokforda":[],
                "propertyRegistrationHistory":[{
                    "registrationDate":"20000101",
                    "countyCode":"12",
                    "municipalityCode":"83",
                    "parishCode":null,
                    "property":{
                        "designation":"Blåkulla",
                        "key":null
                    },
                    "type":{
                        "code":"FB",
                        "description":"Folkbokförd"
                    }
                },{
                    "registrationDate":"20120701",
                    "countyCode":"12",
                    "municipalityCode":"83",
                    "parishCode":null,
                    "property":{
                        "designation":"Blåkulla II",
                        "key":null
                    },
                    "type":{
                        "code":"FB",
                        "description":"Folkbokförd"
                    }
                }]
            }
    END;

    private function getInvalidSessionMock()
    {
        return $this->createConfiguredMock(
            AbstractSession::class,
            [
                'isValid' => false,
                'getAccountName' => 'unknown',
                'get' => false,
                'set' => true,
                'end' => 0
            ],
        );
    }

    private function getValidSessionMock()
    {
        $json = <<<END
        {
            "sn": "sn",
            "title": "title",
            "postalcode": "postalcode",
            "physicaldeliveryofficename": "physicaldeliveryofficename",
            "displayname": "displayname",
            "memberof": "CN=cn,OU=ou,DC=dc",
            "department": "department",
            "company": "company",
            "streetaddress": "streetaddress",
            "useraccountcontrol": "useraccountcontrol",
            "lastlogon": "lastlogon",
            "primarygroupid": "primarygroupid",
            "samaccountname": "samaccountname",
            "userprincipalname": "userprincipalname",
            "mail": "mail",
            "dn": "dn"
        }
        END;

        return $this->createConfiguredMock(
            AbstractSession::class,
            [
                'isValidSession' => true,
                'getAccountName' => 'samaccountname',
                'getSession' => json_decode($json),
                'setSession' => true,
            ],
        );
    }

    public function testSuccessfullSearch(): void
    {
        $request = $this->createPartialMock(Request::class, ['post']);
        $request->expects($this->any())
            ->method('post')->willReturnOnConsecutiveCalls(
                new Response(200, null, json_decode($this->person)),
                new Response(200, null, json_decode($this->relations)),
            );
        // Set auhtorized groups
        $config = new Config(array());

        // Create auth module
        $search = new Search($config, $request, $this->getValidSessionMock());

        // Try authenticate
        $result = $search->find("190001010101");
        // Make sure the values are equals
        $this->assertEquals(json_encode($result), '{"searchFor":"19000101-0101","searchResult":true,"searchResultFamilyRelations":{"0":{"columns":["<a href=\"\/sok\/?action=sok&pnr=2000101010101\"><\/a>","\u2715","\u2715","\u2715 (2012-02-01)","\u2715","\u2715"]}},"searchResultPropertyData":{"title":"Adresshistorik","headings":["Fastighetsbeteckning","H\u00e4ndelse","Datum","Kommunkod","L\u00e4n"],"list":[{"columns":["Bl\u00e5kulla","Folkbokf\u00f6rd","2000-01-01","83","12"]},{"columns":["Bl\u00e5kulla II","Folkbokf\u00f6rd","2012-07-01","83","12"]}]},"basicData":[{"columns":["Personnummer:","19000101-0101"]},{"columns":["K\u00f6n:","Kvinna"]},{"columns":["Civilstatus:","Gift  (2000-01-01)"]},{"columns":["F\u00f6rnamn:","givenName"]},{"columns":["Efternamn:","familyName"]},{"columns":["\u00d6vriga namn:",""]}],"isDeregistered":false,"readableResult":"givenName familyName \u00e4r 124 \u00e5r gammal och \u00e4r bosatt p\u00e5 Streetaddress i Addresslocality.","adressData":[{"columns":["Postort:","Addresslocality"]},{"columns":["Postnummer:","254 50"]},{"columns":["Gatuadress:","Streetaddress"]}]}');
    }
    public function testNoHits(): void
    {
        $mock = $this->createConfiguredMock(
            Request::class,
            [
                'get' => new Response(200, null, null),
                'post' => new Response(404, null, new stdClass()),
            ],
        );
        // Set auhtorized groups
        $config = new Config(array());

        // Create auth module
        $search = new Search($config, $mock, $this->getValidSessionMock());

        // Try authenticate
        $result = $search->find("190001010101");

        // Make sure the values are equals
        $this->assertEquals($result['searchFor'], "19000101-0101");
        $this->assertEquals($result['searchResult'], false);
    }
}
