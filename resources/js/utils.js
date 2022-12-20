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

export const formatNumber = (val, options = {}) => {
  options = {
    digits: 2,
    percentage: false,
    short: true,
    ...options
  }

  if (options.short && val >= 1e3) {
    const lookup = [
      { value: 1e3, symbol: "k" },
      { value: 1e6, symbol: "M" },
      { value: 1e9, symbol: "G" },
      { value: 1e12, symbol: "T" },
      { value: 1e15, symbol: "P" },
      { value: 1e18, symbol: "E" }
    ];

    const rx = /\.0+$|(\.[0-9]*[1-9])0+$/;

    const lookupItem = lookup.slice().reverse().find(function (item) {
      return val >= item.value;
    });

    return (lookupItem ? Number(val / lookupItem.value).toFixed(options.digits).replace(rx, "$1") + lookupItem.symbol : "0") + (options.percentage ? '%' : '');
  }

  return Number(val).toFixed(options.digits) + (options.percentage ? '%' : '')
}