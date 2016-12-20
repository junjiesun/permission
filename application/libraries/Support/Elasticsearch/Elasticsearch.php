<?php
/**
 * https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/index.html
 * https://github.com/navjobs/elastic/blob/master/src/ElasticSearch.php
 * http://wordnetcode.princeton.edu/3.0/WNprolog-3.0.tar.gz
 * 
 */
namespace Lib\Support\Elasticsearch;

use Elasticsearch\ClientBuilder;
use Lib\Support\Elasticsearch\Exceptions\RuntimeException;

class Elasticsearch
{
	
	/**
	 * Client
	 */
	private $client;
	
	/**
	 * Hosts Array
	 */
	protected $hosts = ['127.0.0.1:9200'];
	
	/**
	 * Index 
	 */
	protected $index;
	
	/**
	 * Id
	 */
    protected $id;
	
	/**
	 * Type
	 */
    protected $type;
	
	/**
	 * Fuzzy
	 */
	protected $fuzzy;
	
	/**
	 * Fields
	 */
	protected $fields;
	
	/**
	 * suggest
	 */
	protected $suggest;
	
	/**
	 * Output
	 */
    protected $output;
	

	public function __construct()
	{
		$config = array(
			'Hosts' => $this->hosts
		);
		
		$this->client = ClientBuilder::fromConfig($config);	
		// $this->client = ClientBuilder::create()->build();
	}
	
	public function index( String $index, $data = null )
	{
		$this->index = $index;
		
		if ( empty($data) )
		{
            return $this;
        }
		
		$parameter = [
            'index' => isset($this->index) ? $this->index : null,
            'type'  => isset($this->type) ? $this->type : substr($this->index, 0, -1),
            'body'  => ['doc' => $data]
        ];
		
		if ( isset($this->id) )
		{
            $parameter['id'] = $this->id;
        }
		
		$parameter['body']['doc_as_upsert'] = true;
		
		if ( !$this->exists() )
		{
			$this->create();
		}
		
		return $this->client->update($parameter);
	}
	
	public function exists()
    {
        return $this->client->indices()->exists(['index' => $this->index]);
    }
	
	public function create()
	{
		if ( empty($this->index) )
		{
			throw new RuntimeException('Index Can\'t be empty');
		}
		
		$parameter = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'index' => [
                        'analysis' => [
                            'analyzer' => [
                                'synonym' => [
                                    'tokenizer' => 'whitespace',
                                    'filter' => ['lowercase', 'synonym']
                                ]
                            ],
                            'filter' => [
                                'synonym' => [
                                    'type' => 'synonym',
                                    'format' => 'wordnet',
                                    'synonyms_path' => 'prolog/wn_s.pl'
                                ]
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    '_default_' => [
                        '_all' => [
                            'type' => 'string',
                            'analyzer' => 'synonym'
                        ],
                        'properties' => [
                            'name_suggest' => [
                                'type' =>  'completion',
                                'payloads' => true
                            ]
                        ]
                    ]
                ]
            ]
        ];
		
		$this->client->indices()->create($parameter);
		
		$alias['body'] = [
			'actions' => [
				[
					'add' => [
						'index' => $this->index,
						'alias' => $this->index
					]
				]
			]
		];
		
        return $this->client->indices()->updateAliases($alias);
		
	}
	
	public function delete()
    {
        if ( $this->id )
        {
            $parameter = array();
            $parameter['index']	= isset($this->index) ? $this->index : null;
            $parameter['type']	= isset($this->type) ? $this->type : substr($this->index, 0, -1);
            $parameter['id']	= $this->id;
			
            return $this->client->delete($parameter);
        }
		
        return $this->client->indices()->delete(['index' => $this->index]);
    }
	
	public function get()
    {	
		$parameter = array();
        $parameter['index']	= isset($this->index) ? $this->index : null;
        $parameter['type']	= isset($this->type) ? $this->type : substr($this->index, 0, -1);
        $parameter['id']	= $this->id;
		
        $response = $this->client->get($parameter);
        $result = $response['_source'];
        unset($result['name_suggest']);
        return $result;
    }
	
	public function search( String $term )
	{
		if ( empty($term) )
		{
			throw new RuntimeException('Parameters not null');
		}
		
		$parameter = array();
		$parameter['index'] = isset($this->index) ? $this->index : null;
		$parameter['type'] = isset($this->type) ? $this->type : substr($this->index, 0, -1);
		
		if ( isset($this->fuzzy) )
		{
			$parameter['body']['query']['match']['_all']['query'] = $term;
			$parameter['body']['query']['match']['_all']['fuzziness'] = $this->fuzzy;
		}
		else
		{
			$parameter['body']['query']['match']['_all'] = $term;
		}

		if ( isset($this->fields) )
		{
            $parameter['fields'] = explode(',', str_replace(' ', '', $this->fields));
        }
		
		$response = $this->client->search($parameter);
		$results = array();
		
		foreach ( $response['hits']['hits'] as $item )
		{
            if ( isset( $item['_source'] ) )
            {
                $data = $item['_source'];
                unset($data['name_suggest']);
				
                $data['id'] = $item['_id'];
                $results[] = $data;
            }
            else
            {
                if ( isset( $item['fields'] ) )
                {
                    $fields = array();
                    foreach ( $item['fields'] as $field => $value )
                    {
                        $fields[$field] = $value[0];
                    }
					
                    $data = $fields;
                    $data['id'] = $item['_id'];
                    $results[] = $data;
                }
            }
        }

		return $results;
	}
	
	public function __call( $method, $value )
    {
        $this->$method = $value[0];
        return $this;
    }
	
}


