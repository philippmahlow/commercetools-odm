<?php

namespace BestIt\CommercetoolsODM;

use BestIt\CommercetoolsODM\Repository\ObjectRepository;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;

/**
 * Defines a basic api for setting the repository.
 *
 * @author blange <lange@bestit-online.de>
 * @IgnoreAnnotation("phpcsSuppress")
 * @package BestIt\CommercetoolsODM
 */
interface RepositoryAwareInterface
{
    /**
     * Sets the used repository on the object.
     *
     * @param ObjectRepository $repository
     * @phpcsSuppress BestIt.TypeHints.ReturnTypeDeclaration.MissingReturnTypeHint
     *
     * @return RepositoryAwareInterface
     */
    public function setRepository(ObjectRepository $repository);
}
