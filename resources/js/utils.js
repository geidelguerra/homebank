import { dinero, toDecimal } from 'dinero.js'
import { format as baseFormatDate } from 'date-fns'

export const formatDate = (date, format, options = {}) => {
  if (typeof date === 'string') {
    date = new Date(date)
  }

  return date ? baseFormatDate(date, format, options) : date
}

export const formatMoney = (amount, currency = { code: 'USD', base: 10, exponent: 2 }) => {
  currency = {
    ...currency,
    base: currency.base.length === 1 ? currency.base[0] : currency.base
  }

  return toDecimal(dinero({ amount, currency }))
}