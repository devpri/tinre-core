<script>
import { Line } from 'vue-chartjs'
import axios from 'axios'
import * as moment from 'moment/moment'

export default {
  name: 'clicks-chart',

  extends: Line,

  props: ['url', 'date'],

  data: () => ({
    data: {
      datasets: [],
    },

    options: {
      responsive: true,
      maintainAspectRatio: false,
      elements: {
        line: {
          tension: 0,
        },
      },
      legend: {
        display: false,
      },
      scales: {},
    },
  }),

  mounted() {
    this.getClicks()
  },

  watch: {
    date() {
      if (!this.date || !this.date[0] || !this.date[1]) {
        return
      }

      this.getClicks()
    },
  },

  methods: {
    getClicks() {
      axios
        .get(`stats/${this.url.id}/clicks`, {
          params: { start_date: this.date[0], end_date: this.date[1] },
        })
        .then((response) => {
          const datasetData = response.data.data.map((item) => ({
            x: item.label,
            y: item.value,
          }))

          const dataset = {
            label: this.__('Clicks'),
            backgroundColor: 'transparent',
            borderColor: '#0080ff',
            data: datasetData,
          }

          this.data.datasets = [dataset]

          this.options.scales = {
            xAxes: [
              {
                type: 'time',
                time: {
                  unit: this.getUnit(),
                },
                ticks: {
                  min: this.date[0],
                  max: this.date[1],
                },
              },
            ],
          }

          this.renderChart(this.data, this.options)
        })
    },

    getUnit() {
      const startDate = moment(this.date[0])

      const endDate = moment(this.date[1])

      const difference = endDate.diff(startDate, 'days')

      if (difference === 0) {
        return 'hour'
      }

      if (difference > 1092) {
        return 'year'
      }

      if (difference > 90) {
        return 'month'
      }

      return 'day'
    },
  },
}
</script>
