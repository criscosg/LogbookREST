<?php
namespace EasyScrumREST\SprintBundle\Command;

use EasyScrumREST\SprintBundle\Entity\HoursSprint;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterSprintHoursCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('easyscrum:sprint:hours')
             ->setDescription('Register hours spent in a sprint');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $sprints = $em->getRepository('SprintBundle:Sprint')->findAllActiveSprints();
        $date = new \DateTime('today');
        foreach ($sprints as $sprint) {
            if(!$sprint->getSprintHourbyDate($date)) {
                $hoursSprint = new HoursSprint();
                $hoursSprint->setHours($sprint->getHoursUndone());
                $hoursSprint->setSprint($sprint);
                $hoursSprint->setDate($date);

                $em->persist($hoursSprint);
                $em->flush();
            }
        }
    }
}
