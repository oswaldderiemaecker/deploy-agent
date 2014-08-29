<?php

namespace CphpAgent\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\ViewModel;
use Zend\Config\Config;
use Zend\Http\Response;

use CphpAgent\ConfigAwareInterface;
use CphpAgent\Service\DeployManager as DeployManagerService;

class AgentController extends AbstractActionController implements ConfigAwareInterface, ServiceLocatorAwareInterface
{
    /** @var  Config */
    protected $config;

    /** @var  DeployManagerService */
    private $deployManagerService;


    public function indexAction()
    {
        $config = new Config($this->getConfig());
        $build = $this->getRequest()->getPost('build_id');
        $url = $this->getRequest()->getPost('package_url');
        $project = $this->getRequest()->getPost('project_name');

//        $validator = new \Zend\Validator\Uri(array(
//            'allowRelative' => false
//        ));
//        if (!$validator->isValid($url))
//            return;

        if (!empty($config) && !empty($url) && !empty($build) && !empty($project)) {
            $service = $this->getDeployManagerService();
            $service->deploy($build, $url, $project, $config);
        }


        return new ViewModel(array());
    }

    public function adminAction()
    {
        return new ViewModel(array(//'deployments' => $this->getDeploymentTable()->fetchAll(),
        ));
    }

    /**
     * Getters/setters for DI
     */
    public function getDeployManagerService()
    {
        if (!$this->deployManagerService) {
            $this->deployManagerService = $this->getServiceLocator()->get('cphpagent_deploymanager_service');
        }
        return $this->deployManagerService;
    }

    public function setDeployManageService(DeployManager $deployManagerService)
    {
        $this->deployManagerService = $deployManagerService;
        return $this;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
