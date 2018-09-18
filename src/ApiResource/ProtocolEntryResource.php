<?php

namespace HeimrichHannot\PrivacyApiBundle\ApiResource;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use HeimrichHannot\ApiBundle\ApiResource\ResourceInterface;
use HeimrichHannot\ApiBundle\Security\User\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class ProtocolEntryResource implements ResourceInterface, FrameworkAwareInterface, ContainerAwareInterface
{
    use FrameworkAwareTrait;
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public function create(Request $request, UserInterface $user): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function update($id, Request $request, UserInterface $user): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function list(Request $request, UserInterface $user): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function show($id, Request $request, UserInterface $user): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function delete($id, Request $request, UserInterface $user): ?array
    {
        return null;
    }
}