<?php

namespace NextrasTests\Orm;

use Mockery;
use Nette\DI\Container;
use Nextras\Dbal\Connection;
use Nextras\Orm\Model\IModel;
use Nextras\Orm\TestHelper\TestCaseEntityTrait;
use Tester;


class TestCase extends Tester\TestCase
{
	use TestCaseEntityTrait;

	/** @var Container */
	protected $container;

	/** @var Model */
	protected $orm;

	/** @var string */
	protected $section;


	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	protected function setUp()
	{
		parent::setUp();
		$this->orm = $this->container->getByType(IModel::class);
		$this->section = Helper::getSection();

		if ($this->section === Helper::SECTION_ARRAY) {
			$orm = $this->orm;
			require __DIR__ . "/../db/array-init.php";
		} else {
			Tester\Environment::lock("integration-{$this->section}", TEMP_DIR);
		}
	}


	protected function tearDown()
	{
		parent::tearDown();
		Mockery::close();
	}


	protected function getQueries(callable $callback)
	{
		$conn = $this->container->getByType(Connection::class, false);

		if (!$conn) {
			$callback();
			return [];
		}

		$queries = [];
		$conn->onQuery[__CLASS__] = function ($conn, $sql) use (& $queries) {
			if (!preg_match('#pg_catalog|information_schema|SHOW FULL#', $sql)) {
				$queries[] = $sql;
				echo $sql, "\n";
			}
		};

		try {
			$callback();
			return $queries;

		} finally {
			unset($conn->onQuery[__CLASS__]);
		}
	}
}
