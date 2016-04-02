<?php

/**
 * @testCase
 * @dataProvider ../../../sections.ini
 */

namespace NextrasTests\Orm\Integration\Relationships;

use Mockery;
use Nextras\Orm\Model\IModel;
use NextrasTests\Orm\DataTestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../../bootstrap.php';


class RelationshipOneHasManyCollectionTest extends DataTestCase
{
	public function testRemoveA()
	{
		$queries = $this->getQueries(function () {
			$authorA = $this->orm->authors->getById(1);
			$bookA = $this->orm->books->getById(1);
			Assert::same($authorA, $bookA->author); // THIS FIRES UNNECESSARY QUERY: SELECT * FROM authors WHERE id IN (1)
		});

		if ($queries) {
			Assert::count(2, $queries); // SELECT authorA, SELECT booksA
		}
	}
}


$test = new RelationshipOneHasManyCollectionTest($dic);
$test->run();
