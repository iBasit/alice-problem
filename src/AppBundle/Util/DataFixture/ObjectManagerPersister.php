<?php

declare(strict_types=1);

namespace AppBundle\Util\DataFixture;


use Doctrine\Common\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\Persistence\PersisterInterface;
use Nelmio\Alice\IsAServiceTrait;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @final
 */
/*final*/ class ObjectManagerPersister implements PersisterInterface
{
    use IsAServiceTrait;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var array Values are FQCN of persistable objects
     */
    private $persistableClasses;

    public function __construct(ObjectManager $manager)
    {
        $this->objectManager = $manager;
        $this->persistableClasses = array_flip($this->getPersistableClasses($manager));
    }

    /**
     * @inheritDoc
     */
    public function persist($object)
    {
        var_dump('----perSISI-------');
        if (isset($this->persistableClasses[get_class($object)])) {
            $this->objectManager->persist($object);
        }
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        var_dump('----FLUSH_-------');
        $this->objectManager->flush();
        $this->objectManager->clear();
    }

    /**
     * @return string[]
     */
    private function getPersistableClasses(ObjectManager $manager): array
    {
        $persistableClasses = [];
        $allMetadata = $manager->getMetadataFactory()->getAllMetadata();

        foreach ($allMetadata as $metadata) {
            if (! $metadata->isMappedSuperclass && ! (isset($metadata->isEmbeddedClass) && $metadata->isEmbeddedClass)) {
                $persistableClasses[] = $metadata->getName();
            }
        }

        return $persistableClasses;
    }
}
