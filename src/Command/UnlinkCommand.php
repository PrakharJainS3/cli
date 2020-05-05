<?php

namespace Acquia\Ads\Command;

use Acquia\Ads\Exception\AcquiaCliException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UnlinkCommand.
 */
class UnlinkCommand extends CommandBase {

  /**
   * {inheritdoc}.
   */
  protected function configure() {
    $this->setName('unlink')
      ->setDescription('Remove local association between your project and an Acquia Cloud application');
  }

  /**
   * @return bool
   */
  protected function commandRequiresAuthentication(): bool {
    return FALSE;
  }

  /**
   * @param \Symfony\Component\Console\Input\InputInterface $input
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *
   * @return int 0 if everything went fine, or an exit code
   * @throws \Acquia\Ads\Exception\AcquiaCliException
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->validateCwdIsValidDrupalProject();

    $local_user_config = $this->getDatastore()->get('acquia-cli/user.json');
    $repo_root = $this->getApplication()->getRepoRoot();
    foreach ($local_user_config['localProjects'] as $key => $project) {
      if ($project['directory'] === $repo_root) {
        // @todo Add confirmation.
        unset($local_user_config['localProjects'][$key]);
        $this->localProjectInfo = NULL;
        $this->getDatastore()->set('acquia-cli/user.json', $local_user_config);

        $output->writeln("<info>Unlinked $repo_root from Cloud application {$project['cloud_application_uuid']}</info>");
        return 0;
      }
    }

    throw new AcquiaCliException("This project is not linked to a Cloud application.");
  }

}
