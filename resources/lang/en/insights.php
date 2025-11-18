<?php

return [
    'title' => 'AI Insights',
    'generate_insights' => 'Generate Insights',
    'messages' => [
        'insights_generated' => 'AI insights have been generated successfully.',
        'recommendation_marked_read' => 'Recommendation marked as read.',
        'anomaly_resolved' => 'Anomaly marked as resolved.',
    ],

    'recommendations' => [
        'title' => 'Recommendations',
        'type' => 'Type',
        'content' => 'Content',
        'priority' => 'Priority',
        'status' => 'Status',
        'read' => 'Read',
        'unread' => 'Unread',
        'mark_as_read' => 'Mark as Read',
        'priority_1' => 'Low',
        'priority_2' => 'Medium',
        'priority_3' => 'High',
        'low_savings_rate' => 'Your savings rate is below 10%. Consider increasing your savings to build financial security.',
        'build_emergency_fund' => 'Build an emergency fund covering at least 3 months of expenses.',
        'high_debt_ratio' => 'Your debt-to-income ratio is high. Consider paying down debt or increasing income.',
        'overspending' => 'You are overspending this month. Review your budget and spending habits.',
    ],

    'anomalies' => [
        'title' => 'Anomalies',
        'type' => 'Type',
        'description' => 'Description',
        'severity' => 'Severity',
        'status' => 'Status',
        'detected_at' => 'Detected At',
        'resolved' => 'Resolved',
        'unresolved' => 'Unresolved',
        'mark_resolved' => 'Mark Resolved',
        'unusual_amount' => 'Unusual transaction amount of :amount in :category',
        'duplicate_transaction' => 'Duplicate transaction: :description (:count times)',
        'severity_low' => 'Low',
        'severity_medium' => 'Medium',
        'severity_high' => 'High',
    ],

    'predictions' => [
        'title' => 'Predictions',
        'category' => 'Category',
        'predicted_amount' => 'Predicted Amount',
        'confidence' => 'Confidence',
        'period' => 'Period',
        'prediction_date' => 'Prediction Date',
        'expected_amount' => 'Expected :amount :period',
    ],

    'no_recommendations' => 'No recommendations available.',
    'no_anomalies' => 'No anomalies detected.',
    'no_predictions' => 'No predictions available.',
    'generate_recommendations_hint' => 'Generate insights to get personalized recommendations.',
    'generate_anomalies_hint' => 'Generate insights to detect unusual spending patterns.',
    'generate_predictions_hint' => 'Generate insights to see spending predictions.',
];
