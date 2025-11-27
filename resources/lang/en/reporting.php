<?php

return [
    'title' => 'Custom Reporting',
    'subtitle' => 'Build bespoke dashboards, export insights, and schedule delivery.',
    'dashboard' => [
        'title' => 'Reporting Dashboard',
        'subtitle' => 'Monitor saved reports and manage your widget library.',
        'create_quick' => 'Create Quick Report',
        'reports' => 'Saved Reports',
        'widgets' => 'Widgets',
        'recent_activity' => 'Recent Activity',
        'empty_reports' => 'You have not created any custom reports yet.',
        'empty_widgets' => 'Add widgets in the builder to populate your dashboard.',
    ],
    'metrics' => [
        'income' => 'Total Income',
        'expenses' => 'Total Expenses',
        'net_flow' => 'Net Flow',
        'goals' => 'Active Goals',
    ],
    'builder' => [
        'title' => 'Report Builder',
        'subtitle' => 'Drag & drop widgets to design your ideal dashboard.',
        'available_widgets' => 'Available Widgets',
        'layout_preview' => 'Layout Preview',
        'empty' => 'No widgets added yet. Use the widget library to get started.',
        'instructions' => 'Drag widgets between zones or resize them to fine-tune your layout.',
    ],
    'forms' => [
        'name' => 'Report Name',
        'schedule' => 'Delivery Schedule',
        'format' => 'Default Format',
        'description' => 'Description or filters',
        'submit' => 'Create Report',
        'widget_type' => 'Widget Type',
        'widget_title' => 'Widget Title',
        'widget_size' => 'Widget Size',
        'add_widget' => 'Add Widget',
        'filters' => 'Filters',
        'notes' => 'Notes',
        'report' => 'Report',
        'choose_report' => 'Choose Report',
    ],
    'widgets' => [
        'cash_flow' => [
            'title' => 'Cash Flow',
            'description' => 'Track inflows vs outflows for a selected time period.',
        ],
        'spending_category' => [
            'title' => 'Spending by Category',
            'description' => 'Visualize expenses split by category to catch trends early.',
        ],
        'goal_progress' => [
            'title' => 'Goal Progress',
            'description' => 'Monitor the status of each financial goal and remaining amount.',
        ],
        'budget_health' => [
            'title' => 'Budget Health',
            'description' => 'Highlight budgets that are on track or overspending.',
        ],
        'net_worth' => [
            'title' => 'Net Worth Trend',
            'description' => 'Track net worth movement with projected trajectory.',
        ],
        'custom' => [
            'title' => 'Custom Widget',
            'description' => 'Create a bespoke data tile with manual notes or KPIs.',
        ],
    ],
    'messages' => [
        'report_created' => 'Report created successfully.',
        'widget_created' => 'Widget added successfully.',
        'widget_updated' => 'Widget updated successfully.',
        'widget_deleted' => 'Widget removed.',
        'layout_saved' => 'Layout updated.',
    ],
    'buttons' => [
        'open_builder' => 'Open Builder',
        'export' => 'Export',
        'generate' => 'Generate',
        'manage_widgets' => 'Manage Widgets',
    ],
    'exports' => [
        'title' => 'Report Summary',
        'summary' => 'Summary Metrics',
        'transactions' => 'Recent Transactions',
        'widgets' => 'Widgets',
    ],
    'schedules' => [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'yearly' => 'Yearly',
    ],
    'formats' => [
        'pdf' => 'PDF',
        'excel' => 'Excel',
        'csv' => 'CSV',
    ],
];
