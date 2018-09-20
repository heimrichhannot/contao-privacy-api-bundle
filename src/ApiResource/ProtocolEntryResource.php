<?php

namespace HeimrichHannot\PrivacyApiBundle\ApiResource;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Model;
use Contao\StringUtil;
use Contao\System;
use Firebase\JWT\JWT;
use HeimrichHannot\Ajax\AjaxAction;
use HeimrichHannot\ApiBundle\ApiResource\ResourceInterface;
use HeimrichHannot\ApiBundle\Security\User\UserInterface;
use HeimrichHannot\FormHybrid\FormHybrid;
use HeimrichHannot\Privacy\Manager\ProtocolManager;
use HeimrichHannot\Privacy\Util\ProtocolUtil;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class ProtocolEntryResource implements ResourceInterface, FrameworkAwareInterface, ContainerAwareInterface
{
    use FrameworkAwareTrait;
    use ContainerAwareTrait;

    const MODE_OPT_IN  = 'privacy_protocol_entry_opt_in';
    const MODE_OPT_OUT = 'privacy_protocol_entry_opt_out';

    /**
     * @inheritDoc
     */
    public function create(Request $request, UserInterface $user): ?array
    {
        // data
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return [
                'state'   => 'error',
                'message' => 'No data included.'
            ];
        }

        return $this->doOpt($data, $request, $user);
    }

    protected function doOpt(array $requestData, Request $request, UserInterface $user)
    {
        $protocolManager = new ProtocolManager();
        $protocolUtil    = new ProtocolUtil();
        $app             = $user->getApp();

        if (($protocolArchive = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk(
                'tl_privacy_protocol_archive', $app->privacyProtocolArchive)) === null) {
            return [
                'state'   => 'error',
                'message' => 'Protocol archive not found.'
            ];
        }

        $data                = $protocolUtil->getMappedPrivacyProtocolFieldValues($requestData, deserialize($app->privacyProtocolFieldMapping, true));
        $data['description'] = $app->privacyProtocolDescription;

        if ($app->addPrivacyReferenceEntity) {
            $data['table'] = $protocolArchive->referenceFieldTable;
        }

        $data['cmsCope'] = 'BE';

        $protocolManager->addEntry(
            $app->privacyProtocolEntryType,
            $app->privacyProtocolArchive,
            $data,
            'heimrichhannot/contao-privacy-api-bundle'
        );

        if ($app->addPrivacyActivationProtocolEntry) {
            if (($message = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_nc_message', $app->privacyProtocolActivationNotification)) !== null) {
                foreach ($data as $field => $value) {
                    $tokens['form_value_' . $field] = $value;
                    $tokens['form_plain_' . $field] = $value;
                }

                $token = StringUtil::binToUuid(\Database::getInstance()->getUuid());

                $instance = $protocolUtil->findReferenceEntity(
                    $protocolArchive->referenceFieldTable,
                    $protocolArchive->referenceFieldForeignKey,
                    $data[$protocolArchive->referenceFieldForeignKey]
                );

                if ($instance !== null) {
                    $instance->{FormHybrid::OPT_IN_DATABASE_FIELD} = $token;
                    $instance->save();
                }

                $jwt = JWT::encode([
                    'table'      => $protocolArchive->referenceFieldTable,
                    'token'      => $token,
                    'date'       => time(),
                    'submission' => $data
                ],
                    \Config::get('encryptionKey')
                );

                $tokens['opt_in_token'] = $jwt;

                if (null !== ($jumpTo = System::getContainer()->get('huh.utils.url')->getJumpToPageObject($app->privacyProtocolActivationJumpTo))) {
                    $tokens['opt_in_link'] = System::getContainer()->get('huh.utils.url')->addQueryString(
                        FormHybrid::OPT_IN_REQUEST_ATTRIBUTE . '=' . $jwt,
                        $jumpTo->getFrontendUrl()
                    );
                }

                $tokens['salutation_submission'] = System::getContainer()->get('huh.utils.salutation')->createSalutation(
                    $request->get('lang') ?: $GLOBALS['TL_LANGUAGE'], $data
                );

                $message->send($tokens, $request->get('lang') ?: $GLOBALS['TL_LANGUAGE']);
            }
        } else {
            if ($app->privacyUpdateReferenceEntityFields) {
                $instance = $protocolManager->updateReferenceEntity(
                    $app->privacyProtocolArchive,
                    $data,
                    array_keys($requestData),
                    $app
                );

                if ($instance instanceof Model)
                {
                    System::getContainer()->get('huh.privacy_api.util.privacy_api_util')->addAppCategoriesToModel(
                        $instance, $app
                    );
                }
            }
        }

        return [
            'state'   => 'success',
            'message' => 'Opt-in successful.'
        ];
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