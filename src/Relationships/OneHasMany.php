<?php

/**
 * This file is part of the Nextras\Orm library.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace Nextras\Orm\Relationships;

use Nextras\Orm\Entity\IEntity;


class OneHasMany extends HasMany
{
	public function getEntitiesForPersistence()
	{
		$entities = [];
		foreach ($this->toAdd as $addHash => $add) {
			$entities[$addHash] = $add;
		}
		foreach ($this->toRemove as $removeHash => $remove) {
			if ($remove->isPersisted()) {
				$entities[$removeHash] = $remove;
			}
		}
		if ($this->collection !== null) {
			foreach ($this->collection as $entity) {
				$entities[spl_object_hash($entity)] = $entity;
			}
		} else {
			foreach ($this->referenced as $refereceHash => $reference) {
				$entities[$refereceHash] = $reference;
			}
		}
		return $entities;
	}


	public function doPersist()
	{
		$this->referenced = $this->getEntitiesForPersistence();
		$this->toAdd = [];
		$this->toRemove = [];
		$this->isModified = false;
		$this->collection = null;
	}


	protected function modify()
	{
		$this->isModified = true;
	}


	protected function createCollection()
	{
		$collection = $this->getTargetRepository()->getMapper()->createCollectionOneHasMany($this->metadata, $this->parent);
		return $this->applyDefaultOrder($collection);
	}


	protected function updateRelationshipAdd(IEntity $entity)
	{
		if (!$this->metadata->relationship->property) {
			return;
		}

		$this->updatingReverseRelationship = true;
		$entity->getProperty($this->metadata->relationship->property)->setInjectedValue($this->parent);
		$this->updatingReverseRelationship = false;
	}


	protected function updateRelationshipRemove(IEntity $entity)
	{
		if (!$this->metadata->relationship->property) {
			return;
		}

		$this->updatingReverseRelationship = true;
		$entity->getProperty($this->metadata->relationship->property)->setInjectedValue(null);
		$this->updatingReverseRelationship = false;
	}
}
