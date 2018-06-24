<?php

// ---------------- //
// === TEMPLATE === //
// ---------------- //
$info = array(
	'slug'		=> 'wordpress-site',
	'label' 	=> __('WordPress Site'),
	'author'	=> 'majick777',
	'version'	=> '0.9.0',
);

// --------------- //
// === SECTORS === //
// --------------- //
$sectors = array(
	'planning'		=> array('label' => __('Planning'),			'position' => 10),
	'platform'		=> array('label' => __('Platform'),			'position' => 20),
	'design' 		=> array('label' => __('Design'),			'position' => 30),
	'features'		=> array('label' => __('Features'),			'position' => 40),
	'content'		=> array('label' => __('Content'),			'position' => 50),
	'optimization'	=> array('label' => __('Optimization'),		'position' => 60),
	'marketing'		=> array('label' => __('Marketing'), 		'position' => 70),
	'maintenance'	=> array('label' => __('Maintenance'), 		'position' => 80),
	'operations'	=> array('label' => __('Operations'),		'position' => 90),
);

// -------------- //
// === STAGES === //
// -------------- //
$stages = array(
	'landingpage'	=> array('label' => __('Landing Page'), 	'position' => 10),
	'pre-launch'	=> array('label' => __('Pre Launch'), 		'position' => 30),
	'launch'		=> array('label' => __('Site Launch'),		'position' => 50),
	'post-launch' 	=> array('label' => __('Post Launch'), 		'position' => 70),
	'operating'		=> array('label' => __('Operating'), 		'position' => 90),
);

// ----------------- //
// === DIVISIONS === //
// ----------------- //
$divisions = array(
	'design'		=> array('label' => __('Design'),			'position' => 20),
	'content'		=> array('label' => __('Content'),			'position' => 30),
	'admin'			=> array('label' => __('Administration'),	'position' => 40),
	'develop'		=> array('label' => __('Development'),		'position' => 50),
	'marketing'		=> array('label' => __('Marketing'),		'position' => 60),
	'sales'			=> array('label' => __('Sales'),			'position' => 70),
	'accounting'	=> array('label' => __('Accounting'),		'position' => 80),
	'support'		=> array('label' => __('Support'),			'position' => 90),
);

// === SKILLS === //
// -------------- //
$skills = array(

	// --- Design --- //
	'vision'		=> array('label' => __('Project Vision'),	'division' => 'design'),
	'project'		=> array('label' => __('Project Planning'), 'division' => 'design'),
	'checklists'	=> array('label' => __('Task Checklists'),	'division' => 'design'),
	'structure'		=> array('label' => __('Content Structure'), 'division' => 'design'),
	'features'		=> array('label' => __('Feature Research'), 'division' => 'design'),
	'userexperience' => array('label' => __('User Experience'),	'division' => 'design'),
	'wireframing'	=> array('label' => __('Wireframing'),		'division' => 'design'),

	// --- Content --- //
	'research'		=> array('label' => __('Research'),			'division' => 'content'),
	'drafting'		=> array('label' => __('Drafting'),			'division' => 'content'),
	'writing'		=> array('label' => __('Writing'), 			'division' => 'content'),
	'proofing'		=> array('label' => __('Proofreading'),		'division' => 'content'),
	'editing'		=> array('label' => __('Editing'), 			'division' => 'content'),
	'mockups'		=> array('label' => __('Mockups'),			'division' => 'content'),
	'graphics'		=> array('label' => __('Graphic Design'),	'division' => 'content'),
	'photos'		=> array('label' => __('Photography'),		'division' => 'content'),
	'media'			=> array('label' => __('Multimedia'), 		'division' => 'content'),

	// --- Admin --- //
	'maintenance'	=> array('label' => __('Site Maintenance'),	'division' => 'admin'),
	'monitoring'	=> array('label' => __('Site Monitoring'),	'division' => 'admin'),
	'backups'		=> array('label' => __('Site Backups'),		'division' => 'admin'),
	'installations' => array('label' => __('Software Installs'), 'division' => 'admin'),
	'updates'		=> array('label' => __('Software Updates'),	'division' => 'admin'),
	'commentmod'	=> array('label' => __('Comment Moderation'), 'division' => 'admin'),
	'usermod'		=> array('label' => __('User Moderation'), 	'division' => 'admin'),
	'groupmod'		=> array('label' => __('Group Moderation'), 'division' => 'admin'),
	'testing'		=> array('label' => __('Regular Testing'),	'division' => 'admin'),
	'server'		=> array('label' => __('Server Management'), 'division' => 'admin'),

	// --- Developer --- //
	'html'			=> array('label' => __('HTML'),				'division' => 'develop'),
	'styling'		=> array('label' => __('CSS Styling'),		'division' => 'develop'),
	'javascript'	=> array('label' => __('Javascript'),		'division' => 'develop'),
	'php'			=> array('label' => __('PHP Coding'),		'division' => 'develop'),
	'mysql'			=> array('label' => __('MySQL'),			'division' => 'develop'),
	'interfaces'	=> array('label' => __('Interfaces'),		'division' => 'develop'),
	'solutions'		=> array('label' => __('Solution Research'), 'division' => 'develop'),
	'bugfixing'		=> array('label' => __('Bugfixing'),		'division' => 'develop'),

	// --- Marketing --- //
	'advertising'	=> array('label' => __('Creating Ads'),		'division' => 'marketing'),
	'copywriting'	=> array('label' => __('Copywriting'),		'division' => 'marketing'),
	'markets'		=> array('label' => __('Market Research'),	'division' => 'marketing'),
	'channels'		=> array('label' => __('Channel Research'), 'division' => 'marketing'),
	'promotion'		=> array('label' => __('Promotion Placement'), 'division' => 'marketing'),
	'social'		=> array('label' => __('Social Networking'), 'division' => 'marketing'),
	'affiliates'	=> array('label' => __('Manage Affiliates'), 'division' => 'marketing'),
	'tracking'		=> array('label' => __('Tracking Analysis'), 'division' => 'marketing'),

	// --- Sales --- //
	'emailsales'	=> array('label' => __('Email Sales'), 		'division' => 'sales'),
	'phonesales'	=> array('label' => __('Phone Sales'),		'division' => 'sales'),
	'followup'		=> array('label' => __('Lead Followup'),	'division' => 'sales'),
	'scripts'		=> array('label' => __('Script Creation'),	'division' => 'sales'),
	'conversion'	=> array('label' => __('Conversion Analysis'), 'division' => 'sales'),

	// --- Accounting --- //
	'billing'		=> array('label' => __('Billing Enquiries'), 'division' => 'accounting'),
	'records'		=> array('label' => __('Recordkeeping'), 	'division' => 'accounting'),
	'processing'	=> array('label' => __('Order Processing'),	'division' => 'accounting'),
	'suppliers'		=> array('label' => __('Supplier Research'), 'division' => 'accounting'),
	'ordering'		=> array('label' => __('Bills and Ordering'), 'division' => 'accounting'),
	'stockkeeping'	=> array('label' => __('Stock Keeping'), 	'division' => 'accounting'),
	'analysis'		=> array('label' => __('Account Analysis'), 'division' => 'accounting'),
	'payroll'		=> array('label' => __('Payroll Processing'), 'division' => 'accounting'),
	'commissions'	=> array('label' => __('Affiliate Commissions'), 'division' => 'accounting'),

	// --- Support --- //
	'customers'		=> array('label' => __('Customer Service'), 'division' => 'support'),
	'emails'		=> array('label' => __('Email Handling'), 	'division' => 'support'),
	'technical'		=> array('label' => __('Technical Support'), 'division' => 'support'),
	'bugtesting'	=> array('label' => __('Bug Testing'), 		'division' => 'support'),

);

// populate template array
$template = array(
	'template'		=> $info,
	'sectors' 		=> $sectors,
	'stages'		=> $stages,
	'divisions'		=> $divisions,
	'skills'		=> $skills,
);

