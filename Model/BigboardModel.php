<?php

namespace Kanboard\Plugin\BigBoard\Model;

use Kanboard\Core\Base;
use Kanboard\Model\ProjectModel;

class BigboardModel extends Base
{
    const SELTABLE = 'bigboard_selected';
	const COLTABLE = 'bigboard_collapsed';
	
	
	// SELECT methods :
	// manage list of projects selected to be displayed on the bigboard view
	
    public function selectFindAllProjects($user_id)
    {
        $selectedProjects = $this->db->table(self::SELTABLE)
            ->eq('user_id', $user_id)
            ->in('project_id', $this->db->table(ProjectModel::TABLE)->findAllByColumn('id'))
            ->findAll();

        $projects = array();
        foreach ($selectedProjects as $selectedProject) {
            $projects[] = $this->projectModel->getById($selectedProject['project_id']);
        }

        return $projects;
    }

    public function selectFindAllProjectsById($user_id)
    {
        $selectedProjects = $this->db->table(self::SELTABLE)
            ->eq('user_id', $user_id)
            ->in('project_id', $this->db->table(ProjectModel::TABLE)->findAllByColumn('id'))
            ->asc('position')
            ->asc('id')
            ->findAll();

        $projects = array();
        foreach ($selectedProjects as $selectedProject) {
            $projects[] = $selectedProject['project_id'];
        }
        return $projects;
    }

    public function selectFind($project_id, $user_id)
    {
        $selectedProject = $this->db->table(self::SELTABLE)
            ->eq('user_id', $user_id)
            ->eq('project_id', $project_id)
            ->findOne();

		error_log("BB selectFind selectedProject = ".json_encode($selectedProject)." / project $project_id looked for user $user_id ");
        return $selectedProject;
    }

    public function selectTake($project_id, $user_id)
    {
        $position = $this->getMaxPosition($user_id) + 1;
        $status = $this->db->table(self::SELTABLE)->insert(array(
            'project_id' => $project_id,
            'user_id' => $user_id,
            'position' => $position,
        ));

        error_log("BB selectTake STATUS = $status / project $project_id taken for user $user_id ");

        return $status;
    }

    public function selectDrop($internal_id)
    {
        $status = $this->db->table(self::SELTABLE)
            ->eq('id', $internal_id)
            ->remove();

        error_log("BB selectDrop STATUS = $status droped ( internal_id $internal_id )");

        return !$status;
    }
	
	public function selectClear($user_id)
	{
		        $status = $this->db->table(self::SELTABLE)
            ->eq('user_id', $user_id)
            ->remove();

        error_log("BB selectClear STATUS = $status / cleared for user $user_id ");

        return !$status;
	}

    public function changePosition($user_id, $project_id, $position)
    {
        $count = $this->db->table(self::SELTABLE)
            ->eq('user_id', $user_id)
            ->count();

        if ($position < 1 || $position > $count) {
            return false;
        }

        $projectIds = $this->db->table(self::SELTABLE)
            ->eq('user_id', $user_id)
            ->neq('project_id', $project_id)
            ->asc('position')
            ->asc('id')
            ->findAllByColumn('project_id');

        $offset = 1;
        $results = array();

        foreach ($projectIds as $currentProjectId) {
            if ($offset == $position) {
                $offset++;
            }
            $results[] = $this->db->table(self::SELTABLE)
                ->eq('user_id', $user_id)
                ->eq('project_id', $currentProjectId)
                ->update(array('position' => $offset));
            $offset++;
        }

        $results[] = $this->db->table(self::SELTABLE)
            ->eq('user_id', $user_id)
            ->eq('project_id', $project_id)
            ->update(array('position' => $position));

        return !in_array(false, $results, true);
    }

    public function updatePositions($user_id)
    {
        $position = 0;
        $projects = $this->db->table(self::SELTABLE)
            ->eq('user_id', $user_id)
            ->asc('position')
            ->asc('id')
            ->findAllByColumn('project_id');

        if (!$projects) {
            return false;
        }

        foreach ($projects as $project_id) {
            $this->db->table(self::SELTABLE)
                ->eq('user_id', $user_id)
                ->eq('project_id', $project_id)
                ->update(array('position' => ++$position));
        }

        return true;
    }

    public function getMaxPosition($user_id)
    {
        $max = $this->db->table(self::SELTABLE)
            ->eq('user_id', $user_id)
            ->desc('position')
            ->limit(1)
            ->findOneColumn('position');

        return $max !== false && $max !== null ? (int) $max : 0;
    }

	// COLLAPSE methods :
	// manage status of projects which display as collapsed (or else expanded) on the bigboard view
	// if stored it is collapsed
	
	public function collapseFindAllProjects($user_id)
    {
        $collapsedProjects = $this->db->table(self::COLTABLE)
            ->eq('user_id', $user_id)
            ->findAll();

        $projects = array();
        foreach ($collapsedProjects as $collapsedProject) {
            $projects[] = $this->projectModel->getById($collapsedProject['project_id']);
        }

        return $projects;
    }

    public function collapseFindAllProjectsById($user_id)
    {
        $collapsedProjects = $this->db->table(self::COLTABLE)
            ->eq('user_id', $user_id)
            ->findAll();

        $projects = array();
        foreach ($collapsedProjects as $collapsedProject) {
            $projects[] = $collapsedProject['project_id'];
        }
		sort($projects);
        return $projects;
    }

    public function collapseFind($project_id, $user_id)
    {
        $collapsedProject = $this->db->table(self::COLTABLE)
            ->eq('user_id', $user_id)
            ->eq('project_id', $project_id)
            ->findOne();

		error_log("BB collapseFind collapsedProject = ".json_encode($collapsedProject)." / project $project_id looked for user $user_id ");
        return $collapsedProject;
    }

    public function collapseTake($project_id, $user_id)
    {
        $status = $this->db->table(self::COLTABLE)->insert(array(
            'project_id' => $project_id,
            'user_id' => $user_id,
        ));

        error_log("BB collapseTake STATUS = $status / project $project_id taken for user $user_id ");

        return $status;
    }

    public function collapseDrop($internal_id)
    {
        $status = $this->db->table(self::COLTABLE)
            ->eq('id', $internal_id)
            ->remove();

        error_log("BB collapseDrop STATUS = $status droped ( internal_id $internal_id )");

        return !$status;
    }
	
	public function collapseClear($user_id)
	{
		        $status = $this->db->table(self::COLTABLE)
            ->eq('user_id', $user_id)
            ->remove();

        error_log("BB collapseClear STATUS = $status / cleared for user $user_id ");

        return !$status;
	}	
	
}
