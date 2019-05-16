<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        	new Liip\ImagineBundle\LiipImagineBundle(),
        	new JMS\JobQueueBundle\JMSJobQueueBundle(),
        	new JMS\DiExtraBundle\JMSDiExtraBundle($this),
        	new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Puzzle\AppBundle\AppBundle(),
        	new Puzzle\AdminBundle\AdminBundle(),
        	new Puzzle\UserBundle\UserBundle(),
        	new Puzzle\MediaBundle\MediaBundle(),
        	new Puzzle\ExpertiseBundle\ExpertiseBundle(),
        	new Puzzle\BlogBundle\BlogBundle(),
        	new Puzzle\InventoryBundle\InventoryBundle(),
        	new Puzzle\SaleBundle\SaleBundle(),
        	new Puzzle\NewsletterBundle\NewsletterBundle(),
        	new Puzzle\StaticBundle\StaticBundle(),
        	new Puzzle\SchedulingBundle\SchedulingBundle(),
        	new Puzzle\CollaborativeBundle\CollaborativeBundle(),
        	new Puzzle\CalendarBundle\CalendarBundle(),
        	new Puzzle\BookletBundle\BookletBundle(),
        	new Puzzle\CharityBundle\CharityBundle(),
        	new Puzzle\LearningBundle\LearningBundle(),
            new Puzzle\WalletBundle\WalletBundle(),
            new Puzzle\InsuranceBundle\InsuranceBundle(),
            new Puzzle\BookingBundle\BookingBundle(),
            new Puzzle\ContactBundle\ContactBundle(),
            new Puzzle\TranslationBundle\TranslationBundle(),
        );
        
        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
