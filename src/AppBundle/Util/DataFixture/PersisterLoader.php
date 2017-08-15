<?php


namespace AppBundle\Util\DataFixture;


use AppBundle\Entity\Detail;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PersisterAwareInterface;
use Fidry\AliceDataFixtures\Persistence\PersisterInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Nelmio\Alice\IsAServiceTrait;

/**
 * Loader decorating another loader to add a persistence layer.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @final
 */
/*final*/ class PersisterLoader implements LoaderInterface, PersisterAwareInterface
{
    use IsAServiceTrait;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var PersisterInterface
     */
    private $persister;

    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param LoaderInterface      $decoratedLoader
     * @param PersisterInterface   $persister
     * @param ProcessorInterface[] $processors
     */
    public function __construct(LoaderInterface $decoratedLoader, PersisterInterface $persister, array $processors)
    {
        var_dump(get_class($persister)); // check which method is loaded
        $this->loader = $decoratedLoader;
        $this->persister = $persister;
        $this->processors = (function (ProcessorInterface ...$processors) {
            return $processors;
        })(...$processors);
    }

    /**
     * @inheritdoc
     */
    public function withPersister(PersisterInterface $persister): self
    {
        return new self($this->loader, $persister, $this->processors);
    }

    /**
     * Pre process, persist and post process each object loaded.
     *
     * {@inheritdoc}
     */
    public function load(array $fixturesFiles, array $parameters = [], array $objects = [], PurgeMode $purgeMode = null): array
    {
        $objects = $this->loader->load($fixturesFiles, $parameters, $objects, $purgeMode);

        foreach ($objects as $id => $object) {
            if ($object instanceof Detail) {
                continue;   // Persist it at the second pass
            }

            foreach ($this->processors as $processor) {
                $processor->preProcess($id, $object);
            }

            $this->persister->persist($object);
        }

        $this->persister->flush();

        foreach ($objects as $id => $object) {
            if (!$object instanceof Detail) {
                continue;
            }

            foreach ($this->processors as $processor) {
                $processor->preProcess($id, $object);
            }

            $this->persister->persist($object);
        }
        $this->persister->flush();

        foreach ($objects as $id => $object) {
            foreach ($this->processors as $processor) {
                $processor->postProcess($id, $object);
            }
        }

        return $objects;
    }
}