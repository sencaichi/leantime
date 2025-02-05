<?php

namespace Leantime\Domain\Reports\Controllers {

    use Leantime\Core\Controller;
    use Leantime\Domain\Dashboard\Repositories\Dashboard as DashboardRepository;
    use Leantime\Domain\Projects\Services\Projects as ProjectService;
    use Leantime\Domain\Sprints\Services\Sprints as SprintService;
    use Leantime\Domain\Tickets\Services\Tickets as TicketService;
    use Leantime\Domain\Users\Services\Users as UserService;
    use Leantime\Domain\Timesheets\Services\Timesheets as TimesheetService;
    use Leantime\Domain\Reports\Services\Reports as ReportService;
    use Leantime\Domain\Auth\Services\Auth;
    use Leantime\Domain\Auth\Models\Roles;

    class Show extends Controller
    {
        private DashboardRepository $dashboardRepo;
        private ProjectService $projectService;
        private SprintService $sprintService;
        private TicketService $ticketService;
        private UserService $userService;
        private TimesheetService $timesheetService;
        private ReportService $reportService;

        public function init(
            DashboardRepository $dashboardRepo,
            ProjectService $projectService,
            SprintService $sprintService,
            TicketService $ticketService,
            UserService $userService,
            TimesheetService $timesheetService,
            ReportService $reportService
        ) {
            Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

            $this->dashboardRepo = $dashboardRepo;
            $this->projectService = $projectService;
            $this->sprintService = $sprintService;
            $this->ticketService = $ticketService;
            $this->userService = $userService;
            $this->timesheetService = $timesheetService;

            $_SESSION['lastPage'] = BASE_URL . "/reports/show";

            $this->reportService = $reportService;
            $this->reportService->dailyIngestion();
        }

        /**
         * @return void
         */
        public function get()
        {

            //Project Progress
            $progress = $this->projectService->getProjectProgress($_SESSION['currentProject']);

            $this->tpl->assign('projectProgress', $progress);
            $this->tpl->assign(
                "currentProjectName",
                $this->projectService->getProjectName($_SESSION['currentProject'])
            );

            //Sprint Burndown

            $allSprints = $this->sprintService->getAllSprints($_SESSION['currentProject']);

            $sprintChart = false;

            if ($allSprints !== false && count($allSprints) > 0) {
                if (isset($_GET['sprint'])) {
                    $sprintObject = $this->sprintService->getSprint((int)$_GET['sprint']);
                    $sprintChart = $this->sprintService->getSprintBurndown($sprintObject);
                    $this->tpl->assign('currentSprint', (int)$_GET['sprint']);
                } else {
                    $currentSprint = $this->sprintService->getCurrentSprintId($_SESSION['currentProject']);

                    if ($currentSprint !== false && $currentSprint != "all") {
                        $sprintObject = $this->sprintService->getSprint($currentSprint);
                        $sprintChart = $this->sprintService->getSprintBurndown($sprintObject);
                        $this->tpl->assign('currentSprint', $sprintObject->id);
                    } else {
                        $sprintChart = $this->sprintService->getSprintBurndown($allSprints[0]);
                        $this->tpl->assign('currentSprint', $allSprints[0]->id);
                    }
                }
            }

            $this->tpl->assign('sprintBurndown', $sprintChart);
            $this->tpl->assign('backlogBurndown', $this->sprintService->getCummulativeReport($_SESSION['currentProject']));

            $this->tpl->assign('allSprints', $this->sprintService->getAllSprints($_SESSION['currentProject']));

            $fullReport =  $this->reportService->getFullReport($_SESSION['currentProject']);

            $this->tpl->assign("fullReport", $fullReport);
            $this->tpl->assign("fullReportLatest", $this->reportService->getRealtimeReport($_SESSION['currentProject'], ""));

            $this->tpl->assign('states', $this->ticketService->getStatusLabels());

            //Milestones

            $allProjectMilestones = $this->ticketService->getAllMilestones(["sprint" => '', "type" => "milestone", "currentProject" => $_SESSION["currentProject"]]);
            $this->tpl->assign('milestones', $allProjectMilestones);

            $this->tpl->display('reports.show');
        }

        public function post($params)
        {

            $this->tpl->redirect(BASE_URL . "/dashboard/show");
        }
    }
}
