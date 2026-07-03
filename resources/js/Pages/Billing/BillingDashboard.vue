<script setup lang="ts">
import { onMounted } from 'vue';
import { useBillingStore } from '@/stores/billingStore';
import { pinia } from '../../app.js';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"; // Импортируем экземпляр pinia из app.js

const billingStore = useBillingStore(pinia);

onMounted(() => {
    billingStore.fetchDashboard();
});

const handlePurchase = async (tierId: number) => {
    const success = await billingStore.buySubscription(tierId);
    if (success) {
        alert('Success! Invoice is being generated asynchronously via Redis Worker queue.');
    }
};
</script>

<template>
   <AuthenticatedLayout>
       <div class="min-h-screen bg-gray-900 p-8 text-gray-100">
           <!-- Global Loading Indicator -->
           <div v-if="billingStore.isLoading" class="fixed top-4 right-4 bg-indigo-600 px-4 py-2 rounded shadow-md text-xs font-bold animate-pulse">
               HYDRATING STATE...
           </div>

           <div class="max-w-6xl mx-auto space-y-8">
               <!-- Header Grid Area -->
               <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                   <!-- Ledger Balance Component Card -->
                   <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-xl">
                       <p class="text-sm font-semibold tracking-wider text-gray-400 uppercase">Secure Wallet Ledger Balance</p>
                       <p class="text-4xl font-extrabold text-green-400 mt-2">${{ billingStore.walletBalance.toFixed(2) }}</p>
                   </div>

                   <!-- API Server Error Boundary Component -->
                   <div v-if="billingStore.errorMessage" class="bg-red-900/50 border border-red-500 p-6 rounded-xl text-red-200">
                       <span class="font-bold block text-sm tracking-wider uppercase mb-1">Validation / Operation Error</span>
                       {{ billingStore.errorMessage }}
                   </div>
               </div>

               <!-- Main Interactive Tiers Component -->
               <div>
                   <h3 class="text-xl font-bold mb-4 tracking-wide text-indigo-400">Available Corporate Tiers</h3>
                   <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                       <div v-for="tier in billingStore.availableTiers" :key="tier.id" class="bg-gray-800 border border-gray-700 p-6 rounded-xl flex flex-col justify-between hover:border-indigo-500 transition-colors">
                           <div>
                               <h4 class="text-lg font-bold text-white">{{ tier.name }}</h4>
                               <p class="text-2xl font-black text-indigo-400 mt-2">${{ tier.price }} <span class="text-xs text-gray-400 font-normal">/ {{ tier.duration_days }} Days</span></p>
                           </div>
                           <button
                               @click="handlePurchase(tier.id)"
                               :disabled="billingStore.isLoading"
                               class="mt-6 w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-700 text-white font-semibold rounded-lg transition text-sm shadow-md"
                           >
                               Purchase via Ledger Account
                           </button>
                       </div>
                   </div>
               </div>

               <!-- Eager Loaded Relationships Array View (N+1 Free) -->
               <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                   <h3 class="text-lg font-bold mb-4 text-gray-300">Audited Ledger Transaction Log (Eager Loaded)</h3>
                   <div class="overflow-x-auto">
                       <table class="w-full text-left text-sm text-gray-400">
                           <thead class="bg-gray-900 text-gray-300 uppercase text-xs tracking-wider">
                           <tr>
                               <th class="p-3 rounded-l-lg">ID</th>
                               <th class="p-3">Internal Execution Intent</th>
                               <th class="p-3 rounded-r-lg">Calculated Delta</th>
                           </tr>
                           </thead>
                           <tbody class="divide-y divide-gray-700">
                           <tr v-for="tx in billingStore.transactions" :key="tx.id" class="hover:bg-gray-700/30">
                               <td class="p-3 font-mono text-gray-500">#{{ tx.id }}</td>
                               <td class="p-3 font-medium text-gray-300">{{ tx.type }}</td>
                               <td class="p-3 font-bold" :class="tx.amount < 0 ? 'text-red-400' : 'text-green-400'">
                                   {{ tx.amount < 0 ? '-' : '+' }}${{ Math.abs(tx.amount).toFixed(2) }}
                               </td>
                           </tr>
                           <tr v-if="billingStore.transactions.length === 0">
                               <td colspan="3" class="p-4 text-center text-gray-500 italic">No historical adjustments tracked in ledger model scope.</td>
                           </tr>
                           </tbody>
                       </table>
                   </div>
               </div>
           </div>
       </div>
   </AuthenticatedLayout>
</template>
