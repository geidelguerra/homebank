import { dinero, toDecimal as baseToDecimal, toSnapshot as baseToSnapshop, toSnapshot } from 'dinero.js'
import { format as baseFormatDate } from 'date-fns'
import { USD } from '@dinero.js/currencies'

export const date = (val) => {
  if (typeof val === 'string') {
    val = new Date(val)
  }

  return {
    format: (format, options = {}) => baseFormatDate(val, format, options)
  }
}

export const money = (amount, currency = USD) => {
  amount = parseInt(amount)

  currency = {
    ...currency,
    base: currency.base.length === 1 ? currency.base[0] : currency.base
  }

  const val = dinero({ amount, currency })

  return {
    toDecimal: () => baseToDecimal(val),
    toSnapshot: () => baseToSnapshop(val)
  }
}

export const parseMoney = (amount, currency = USD) => {
  return money(Math.round(parseFloat(amount) * (currency.base ** currency.exponent)), currency)
}

export const formatMoney = (amount, currency = { code: 'USD', base: 10, exponent: 2 }) => {
  currency = {
    ...currency,
    base: currency.base.length === 1 ? currency.base[0] : currency.base
  }

  const money = dinero({ amount: parseFloat(amount), currency })

  return asRaw ? toSnapshot(money).amount : toDecimal(money)
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