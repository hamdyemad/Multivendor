<?php

/**
 * Order Stage Transition Rules
 * 
 * Defines which stages can transition to which other stages
 * Format: 'from_slug' => ['to_slug_1', 'to_slug_2', ...]
 * 
 * System Stages:
 * - new: Initial stage when order is created
 * - in-progress: Order is being prepared (goes to fulfillment)
 * - deliver: Order is ready for delivery
 * - cancel: Order is cancelled
 * - want-to-return: Customer wants to return
 * - in-progress-return: Return is being processed
 * - refund: Refund completed
 */

return [
    'transitions' => [
        // From "New" stage
        'new' => [
            'in-progress',      // Can move to In Progress
            'cancel',           // Can cancel before processing
            'want-to-return',   // Can request return
        ],

        // From "In Progress" stage
        'in-progress' => [
            'deliver',          // Can move to Deliver
            'cancel',           // Can cancel during processing
        ],

        // From "Deliver" stage
        'deliver' => [
            'want-to-return',   // Can request return after delivery
            'in-progress-return', // Move to processing return
            'refund',
        ],

        // From "Cancel" stage
        'cancel' => [
            // No transitions from cancelled state
        ],

        // From "Want To Return" stage
        'want-to-return' => [
            'in-progress-return', // Move to processing return
            'refund',
        ],

        // From "In Progress Return" stage
        'in-progress-return' => [
            'refund',           // Complete the return with refund
        ],

        // From "Refund" stage
        'refund' => [
            // No transitions from refund state
        ],
    ],

    /**
     * Stages that require fulfillment page
     * When an order moves to these stages, redirect to fulfillment page
     */
    'fulfillment_stages' => [
        'in-progress',
    ],

    /**
     * Terminal stages (no further transitions possible)
     */
    'terminal_stages' => [
        'cancel',
        'refund',
    ],
];
