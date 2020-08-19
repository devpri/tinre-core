import * as moment from 'moment/moment'
import Localization from '@/mixins/Localization'

const shortcuts = [
  {
    text: Localization.methods.__('Today'),
    onClick: () => [
      moment().startOf('day').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: Localization.methods.__('Yesterday'),
    onClick: () => [
      moment().subtract(1, 'days').startOf('day').toDate(),
      moment().subtract(1, 'days').endOf('day').toDate(),
    ],
  },
  {
    text: Localization.methods.__('This week'),
    onClick: () => [
      moment().startOf('week').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: Localization.methods.__('Last 7 Days'),
    onClick: () => [
      moment().subtract(7, 'days').startOf('day').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: Localization.methods.__('Last Week'),
    onClick: () => [
      moment().subtract(1, 'weeks').startOf('week').toDate(),
      moment().subtract(1, 'weeks').endOf('week').toDate(),
    ],
  },
  {
    text: Localization.methods.__('Last 30 Days'),
    onClick: () => [
      moment().subtract(30, 'days').startOf('day').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: Localization.methods.__('Last Month'),
    onClick: () => [
      moment().subtract(1, 'months').startOf('month').toDate(),
      moment().subtract(1, 'months').endOf('month').toDate(),
    ],
  },
  {
    text: Localization.methods.__('This Year'),
    onClick: () => [
      moment().startOf('year').toDate(),
      moment().endOf('day').toDate(),
    ],
  },
  {
    text: Localization.methods.__('Last Year'),
    onClick: () => [
      moment().subtract(1, 'years').startOf('year').toDate(),
      moment().subtract(1, 'years').endOf('year').toDate(),
    ],
  },
]

export default shortcuts
