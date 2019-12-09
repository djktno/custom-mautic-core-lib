<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Mautic\AssetBundle\EventListener\BuilderSubscriber;
use Mautic\AssetBundle\EventListener\ReportSubscriber;

return [
    'routes' => [
        'main' => [
            'mautic_asset_index' => [
                'path'       => '/assets/{page}',
                'controller' => 'MauticAssetBundle:Asset:index',
            ],
            'mautic_asset_remote' => [
                'path'       => '/assets/remote',
                'controller' => 'MauticAssetBundle:Asset:remote',
            ],
            'mautic_asset_action' => [
                'path'       => '/assets/{objectAction}/{objectId}',
                'controller' => 'MauticAssetBundle:Asset:execute',
            ],
        ],
        'api' => [
            'mautic_api_assetsstandard' => [
                'standard_entity' => true,
                'name'            => 'assets',
                'path'            => '/assets',
                'controller'      => 'MauticAssetBundle:Api\AssetApi',
            ],
        ],
        'public' => [
            'mautic_asset_download' => [
                'path'       => '/asset/{slug}',
                'controller' => 'MauticAssetBundle:Public:download',
                'defaults'   => [
                    'slug' => '',
                ],
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'items' => [
                'mautic.asset.assets' => [
                    'route'    => 'mautic_asset_index',
                    'access'   => ['asset:assets:viewown', 'asset:assets:viewother'],
                    'parent'   => 'mautic.core.components',
                    'priority' => 300,
                ],
            ],
        ],
    ],

    'categories' => [
        'asset' => null,
    ],

    'services' => [
        'events' => [
            'mautic.asset.subscriber' => [
                'class'     => \Mautic\AssetBundle\EventListener\AssetSubscriber::class,
                'arguments' => [
                    'mautic.helper.ip_lookup',
                    'mautic.core.model.auditlog',
                ],
            ],
            'mautic.asset.pointbundle.subscriber' => [
                'class'     => \Mautic\AssetBundle\EventListener\PointSubscriber::class,
                'arguments' => [
                    'mautic.point.model.point',
                ],
            ],
            'mautic.asset.formbundle.subscriber' => [
                'class' => \Mautic\AssetBundle\EventListener\FormSubscriber::class,
            ],
            'mautic.asset.campaignbundle.subscriber' => [
                'class'     => \Mautic\AssetBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'mautic.campaign.model.event',
                ],
            ],
            'mautic.asset.reportbundle.subscriber' => [
                'class'     => ReportSubscriber::class,
                'arguments' => [
                    'mautic.lead.model.company_report_data',
                    'mautic.asset.repository.download',
                ],
            ],
            'mautic.asset.builder.subscriber' => [
                'class'     => BuilderSubscriber::class,
                'arguments' => [
                    'mautic.asset.helper.token',
                    'mautic.tracker.contact',
                    'mautic.helper.token_builder.factory',
                ],
            ],
            'mautic.asset.leadbundle.subscriber' => [
                'class'     => \Mautic\AssetBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'mautic.asset.model.asset',
                    'translator',
                    'router',
                    'mautic.asset.repository.download',
                ],
            ],
            'mautic.asset.pagebundle.subscriber' => [
                'class' => \Mautic\AssetBundle\EventListener\PageSubscriber::class,
            ],
            'mautic.asset.emailbundle.subscriber' => [
                'class' => \Mautic\AssetBundle\EventListener\EmailSubscriber::class,
            ],
            'mautic.asset.configbundle.subscriber' => [
                'class' => \Mautic\AssetBundle\EventListener\ConfigSubscriber::class,
            ],
            'mautic.asset.search.subscriber' => [
                'class'     => \Mautic\AssetBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'mautic.asset.model.asset',
                    'mautic.security',
                    'mautic.helper.user',
                    'mautic.helper.templating',
                ],
            ],
            'mautic.asset.stats.subscriber' => [
                'class'     => \Mautic\AssetBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'mautic.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'oneup_uploader.pre_upload' => [
                'class'     => \Mautic\AssetBundle\EventListener\UploadSubscriber::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.asset.model.asset',
                    'mautic.core.validator.file_upload',
                ],
            ],
            'mautic.asset.dashboard.subscriber' => [
                'class'     => \Mautic\AssetBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'mautic.asset.model.asset',
                    'router',
                ],
            ],
            'mautic.asset.subscriber.determine_winner' => [
                'class'     => \Mautic\AssetBundle\EventListener\DetermineWinnerSubscriber::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'translator',
                ],
            ],
        ],
        'forms' => [
            'mautic.form.type.asset' => [
                'class'     => \Mautic\AssetBundle\Form\Type\AssetType::class,
                'arguments' => [
                    'translator',
                    'mautic.asset.model.asset',
                ],
            ],
            'mautic.form.type.pointaction_assetdownload' => [
                'class' => \Mautic\AssetBundle\Form\Type\PointActionAssetDownloadType::class,
            ],
            'mautic.form.type.campaignevent_assetdownload' => [
                'class' => \Mautic\AssetBundle\Form\Type\CampaignEventAssetDownloadType::class,
            ],
            'mautic.form.type.formsubmit_assetdownload' => [
                'class' => \Mautic\AssetBundle\Form\Type\FormSubmitActionDownloadFileType::class,
            ],
            'mautic.form.type.assetlist' => [
                'class'     => \Mautic\AssetBundle\Form\Type\AssetListType::class,
                'arguments' => [
                    'mautic.security',
                    'mautic.asset.model.asset',
                    'mautic.helper.user',
                ],
            ],
            'mautic.form.type.assetconfig' => [
                'class' => \Mautic\AssetBundle\Form\Type\ConfigType::class,
            ],
            'mautic.form.type.asset_dashboard_downloads_in_time_widget' => [
                'class' => \Mautic\AssetBundle\Form\Type\DashboardDownloadsInTimeWidgetType::class,
            ],
        ],
        'others' => [
            'mautic.asset.upload.error.handler' => [
                'class'     => 'Mautic\AssetBundle\ErrorHandler\DropzoneErrorHandler',
                'arguments' => 'mautic.factory',
            ],
            // Override the DropzoneController
            'oneup_uploader.controller.dropzone.class' => 'Mautic\AssetBundle\Controller\UploadController',
            'mautic.asset.helper.token'                => [
                'class'     => 'Mautic\AssetBundle\Helper\TokenHelper',
                'arguments' => 'mautic.asset.model.asset',
            ],
        ],
        'models' => [
            'mautic.asset.model.asset' => [
                'class'     => \Mautic\AssetBundle\Model\AssetModel::class,
                'arguments' => [
                    'mautic.lead.model.lead',
                    'mautic.category.model.category',
                    'request_stack',
                    'mautic.helper.ip_lookup',
                    'mautic.helper.core_parameters',
                    'mautic.lead.service.device_creator_service',
                    'mautic.lead.factory.device_detector_factory',
                    'mautic.lead.service.device_tracking_service',
                ],
            ],
        ],
        'repositories' => [
            'mautic.asset.repository.download' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => \Mautic\AssetBundle\Entity\Download::class,
            ],
        ],
    ],

    'parameters' => [
        'upload_dir'         => '%kernel.root_dir%/../media/files',
        'max_size'           => '6',
        'allowed_extensions' => ['csv', 'doc', 'docx', 'epub', 'gif', 'jpg', 'jpeg', 'mpg', 'mpeg', 'mp3', 'odt', 'odp', 'ods', 'pdf', 'png', 'ppt', 'pptx', 'tif', 'tiff', 'txt', 'xls', 'xlsx', 'wav'],
    ],
];
