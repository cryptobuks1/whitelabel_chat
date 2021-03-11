export function shortTimeAgo (date, locale, converterOptions) {
    const prefix = converterOptions.prefix || ''
    const suffix = converterOptions.suffix || 'ago'
    const yearSuffix = converterOptions.yearSuffix || 'y'
    const monthSuffix = converterOptions.monthSuffix || 'm'
    const daySuffix = converterOptions.daySuffix || 'd'
    const hourSuffix = converterOptions.hourSuffix || 'h'
    const minuteSuffix = converterOptions.minuteSuffix || 'min'
    const diffSpacer = converterOptions.diffSpacer || ''

    let difference = new Date().getTime() -
        new Date(Date.parse(date)).getTime()

    const yearsDifference = Math.floor(
        difference / 1000 / 60 / 60 / 24 / 30 / 12)
    difference -= yearsDifference * 1000 * 60 * 60 * 24 * 30 * 12

    if (yearsDifference > 0) {
        return `${prefix} ${yearsDifference}${diffSpacer}${yearSuffix} ${suffix}`
    }

    const monthsDifference = Math.floor(difference / 1000 / 60 / 60 / 24 / 30)
    difference -= monthsDifference * 1000 * 60 * 60 * 24 * 30

    if (monthsDifference > 0) {
        return `${prefix} ${monthsDifference}${diffSpacer}${monthSuffix} ${suffix}`
    }

    const daysDifference = Math.floor(difference / 1000 / 60 / 60 / 24)
    difference -= daysDifference * 1000 * 60 * 60 * 24

    if (daysDifference > 0) {
        return `${prefix} ${daysDifference}${diffSpacer}${daySuffix} ${suffix}`
    }

    const hoursDifference = Math.floor(difference / 1000 / 60 / 60)
    difference -= hoursDifference * 1000 * 60 * 60

    if (hoursDifference > 0) {
        return `${prefix} ${hoursDifference}${diffSpacer}${hourSuffix} ${suffix}`
    }

    const minutesDifference = Math.floor(difference / 1000 / 60)
    difference -= minutesDifference * 1000 * 60

    if (minutesDifference > 0) {
        return `${prefix} ${minutesDifference}${diffSpacer}${minuteSuffix} ${suffix}`
    }

    return `${prefix} a ${minuteSuffix} ${suffix}`
}
