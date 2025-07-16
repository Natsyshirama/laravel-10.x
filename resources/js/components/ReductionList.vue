<template>
    <div>
        <h2>Liste des Réductions</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mois</th>
                    <th>Valeur (%)</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="reduction in reductions" :key="reduction.id">
                    <td>{{ reduction.id }}</td>
                    <td>{{ formatDate(reduction.mois) }}</td>
                    <td>{{ reduction.valeur }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            reductions: []
        };
    },
    mounted() {
        this.fetchReductions();
    },
    methods: {
        async fetchReductions() {
            try {
                const response = await fetch('/api/reductions');
                this.reductions = await response.json();
            } catch (error) {
                console.error('Erreur récupération réductions', error);
            }
        },
        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long' });
        }
    }
}
</script>
