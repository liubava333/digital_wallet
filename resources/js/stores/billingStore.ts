import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useBillingStore = defineStore('billing', () => {
    const walletBalance = ref<number>(0);
    const transactions = ref<any[]>([]);
    const subscriptions = ref<any[]>([]);
    const availableTiers = ref<any[]>([]);
    const isLoading = ref<boolean>(false);
    const errorMessage = ref<string | null>(null);

    const hasActiveSubscription = computed(() => subscriptions.value.length > 0);

    async function fetchDashboard() {
        isLoading.value = true;
        errorMessage.value = null;
        try {
            const response = await axios.get('/billing/dashboard-data');
            walletBalance.value = response.data.wallet_balance;
            transactions.value = response.data.transactions;
            subscriptions.value = response.data.subscriptions;
            availableTiers.value = response.data.available_tiers;
        } catch (error: any) {
            errorMessage.value = error.response?.data?.error || 'Failed hydrating billing data.';
        } finally {
            isLoading.value = false;
        }
    }

    async function buySubscription(tierId: number) {
        isLoading.value = true;
        errorMessage.value = null;
        try {
            await axios.post('/billing/subscribe', { subscription_tier_id: tierId });
            await fetchDashboard(); // Reactive structural rehydration
            return true;
        } catch (error: any) {
            errorMessage.value = error.response?.data?.error || 'Transaction rejected.';
            return false;
        } finally {
            isLoading.value = false;
        }
    }

    return {
        walletBalance, transactions, subscriptions, availableTiers, isLoading, errorMessage,
        hasActiveSubscription, fetchDashboard, buySubscription
    };
});
