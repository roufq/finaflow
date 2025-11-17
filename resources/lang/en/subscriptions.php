<?php

return [
    'title' => 'Subscription Management',
    'buttons' => [
        'add_subscription' => 'Add Subscription',
        'save' => 'Save Subscription',
        'update' => 'Update Subscription',
    ],
    'summary' => [
        'total' => 'Total Subscriptions',
        'monthly_cost' => 'Monthly Cost',
        'renewing_soon' => 'Renewing Soon',
        'potential_savings' => 'Potential Savings',
    ],
    'table' => [
        'heading' => 'Your Subscriptions',
        'columns' => [
            'name' => 'Name',
            'provider' => 'Provider',
            'amount' => 'Amount',
            'frequency' => 'Frequency',
            'next_billing' => 'Next Billing',
            'status' => 'Status',
            'actions' => 'Actions',
        ],
        'empty' => 'No subscriptions added yet.',
    ],
    'badges' => [
        'auto' => 'Auto-renew',
        'manual' => 'Manual',
    ],
    'empty' => [
        'title' => 'No Subscriptions Added',
        'description' => 'Start tracking your subscriptions to better manage recurring expenses.',
        'cta' => 'Add Your First Subscription',
    ],
    'tips' => [
        'title' => 'Optimization Suggestions',
        'bundle_title' => 'Bundle Opportunities:',
        'bundle_message' => 'Consider bundling similar services or switching to annual plans for potential savings of Rp :amount per month.',
        'smart_title' => 'Smart Tip:',
        'smart_message' => 'Review subscriptions you haven\'t used in the last 30 days. Canceling unused services could save you money.',
    ],
    'messages' => [
        'delete_confirm' => 'Are you sure you want to delete this subscription?',
        'created' => 'Subscription created successfully.',
        'updated' => 'Subscription updated successfully.',
        'deleted' => 'Subscription deleted successfully.',
        'paused' => 'Subscription paused successfully.',
        'resumed' => 'Subscription resumed successfully.',
        'cancelled' => 'Subscription cancelled successfully.',
    ],
    'form' => [
        'details' => 'Subscription Details',
        'name' => 'Subscription Name',
        'provider' => 'Provider',
        'amount' => 'Amount (Rp)',
        'frequency' => 'Billing Frequency',
        'category' => 'Category',
        'start_date' => 'Start Date',
        'next_billing' => 'Next Billing Date',
        'notes' => 'Notes',
    ],
];
