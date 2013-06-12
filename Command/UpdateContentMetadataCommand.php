<?php
/**
 * File containing the CreateContentCommand class.
 *
 * @copyright Copyright (C) 2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace EzSystems\CookbookBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption;

class UpdateContentMetadataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName( 'ezpublish:cookbook:update_content_metadata' )->setDefinition(
            array(
                new InputArgument( 'contentId' , InputArgument::REQUIRED, 'the content to be updated' ),
                new InputArgument( 'newOwnerId' , InputArgument::REQUIRED, 'the new title of the content' ),
                new InputArgument( 'newModificationDate' , InputArgument::REQUIRED, 'the new body of the content' ),
                new InputArgument( 'newPublishedDate' , InputArgument::REQUIRED, 'the new body of the content' ),
            )
        );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        /** @var $repository \eZ\Publish\API\Repository\Repository */
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );
        $contentService = $repository->getContentService();

        $repository->setCurrentUser( $repository->getUserService()->loadUser( 14 ) );

        $contentId = $input->getArgument( 'contentId' );
        $newOwnerId = $input->getArgument( 'newOwnerId' );
        $newModificationDate = $input->getArgument( 'newModificationDate' );
        $newPublishedDate = $input->getArgument( 'newPublishedDate' );

        try
        {
            // load content info
            $contentInfo = $contentService->loadContentInfo( $contentId );

            // instantiate a content metadata update struct and set the fields
            $contentMetadataUpdateStruct = $contentService->newContentMetadataUpdateStruct();
            $contentMetadataUpdateStruct->ownerId = $newOwnerId;
            $contentMetadataUpdateStruct->modificationDate = new \DateTime($newModificationDate);
            $contentMetadataUpdateStruct->publishedDate = new \DateTime($newPublishedDate);

            // update the content info
            $content = $contentService->updateContentMetadata($contentInfo, $contentMetadataUpdateStruct);

            print_r( $contentInfo );
        }
        catch ( \eZ\Publish\API\Repository\Exceptions\NotFoundException $e )
        {
            $output->writeln( $e->getMessage() );
        }
        catch( \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException $e )
        {
            $output->writeln( $e->getMessage() );
        }
        catch( \eZ\Publish\API\Repository\Exceptions\ContentValidationException $e )
        {
            $output->writeln( $e->getMessage() );
        }
    }
}
