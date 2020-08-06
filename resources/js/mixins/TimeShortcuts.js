import * as moment from 'moment/moment'

const shortcuts = [
  {
    text: 'Today',
    onClick: () => [
      moment().startOf('day').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: 'Yesterday',
    onClick: () => [
      moment().subtract(1, 'days').startOf('day').toDate(),
      moment().subtract(1, 'days').endOf('day').toDate(),
    ],
  },
  {
    text: 'This week',
    onClick: () => [
      moment().startOf('week').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: 'Last 7 Days',
    onClick: () => [
      moment().subtract(7, 'days').startOf('day').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: 'Last Week',
    onClick: () => [
      moment().subtract(1, 'weeks').startOf('week').toDate(),
      moment().subtract(1, 'weeks').endOf('week').toDate(),
    ],
  },
  {
    text: 'Last 30 Days',
    onClick: () => [
      moment().subtract(30, 'days').startOf('day').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: 'Last Month',
    onClick: () => [
      moment().subtract(1, 'months').startOf('month').toDate(),
      moment().subtract(1, 'months').endOf('month').toDate(),
    ],
  },
  {
    text: 'This Year',
    onClick: () => [
      moment().startOf('year').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: 'Last Year',
    onClick: () => [
      moment().subtract(1, 'years').startOf('year').toDate(),
      moment().subtract(1, 'years').endOf('year').toDate(),
    ],
  },
]

export default shortcuts
