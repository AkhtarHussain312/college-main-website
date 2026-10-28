<script setup>
defineProps({
  fees: {
    type: Array,
    default: () => [],
  },
  compact: Boolean,
});

const money = (fee) => `${fee.currency || 'PKR'} ${Number(fee.amount || 0).toLocaleString()}`;

const formatBasis = (basis) => {
  if (!basis) return '';
  const b = String(basis).toLowerCase().trim();
  if (b === 'one time' || b === 'one-time') return 'One-time';
  if (b === 'per year' || b === 'annual') return 'Per Year';
  if (b === 'per semester' || b === 'semester') return 'Per Semester';
  return basis;
};
</script>

<template>
  <div v-if="fees.length" class="fee-breakdown" :class="{ compact }">
    <div class="fee-heading">
      <span><i class="bi bi-cash-stack"></i> Fee Structure</span>
      <small>{{ fees.length }} {{ fees.length === 1 ? 'charge' : 'charges' }}</small>
    </div>
    <div class="fee-rows">
      <div
        v-for="fee in fees"
        :key="fee.id || fee.type || fee.fee_type"
        class="fee-row"
      >
        <span class="fee-row-label">
          {{ fee.type || fee.fee_type || 'Tuition Fee' }}
          <small v-if="fee.basis || fee.billing_basis" class="fee-row-basis">
            {{ formatBasis(fee.basis || fee.billing_basis) }}
          </small>
        </span>
        <b class="fee-row-amount">{{ money(fee) }}</b>
      </div>
    </div>
    <p v-if="!compact && fees.some((f) => f.notes)" class="fee-notes">
      {{ fees.find((f) => f.notes)?.notes }}
    </p>
  </div>
</template>
