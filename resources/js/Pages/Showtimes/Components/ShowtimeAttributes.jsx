export default function ShowtimeAttributes({data, setData}){
    return(
            <div className="border-b border-white/10 pb-12">
                <div className="mt-10 space-y-10">
                    <fieldset>
                    <legend className="text-sm/6 font-semibold text-white">Showtime Attributes</legend>
                    <div className="mt-6 space-y-6">
                        <div className="flex gap-3">
                            <div className="flex h-6 shrink-0 items-center">
                                <div className="group grid size-4 grid-cols-1">
                                <input
                                    id="subtitles"
                                    name="subtitles"
                                    type="checkbox"
                                    checked={data.subtitles}
                                    onChange={e => setData('subtitles', e.target.checked)}
                                    aria-describedby="subtitles-description"
                                    className="col-start-1 row-start-1 appearance-none rounded border border-white/10 bg-white/5 checked:border-indigo-500 checked:bg-indigo-500 indeterminate:border-indigo-500 indeterminate:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 disabled:border-white/5 disabled:bg-white/10 disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                />
                                <svg
                                    fill="none"
                                    viewBox="0 0 14 14"
                                    className="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-[:disabled]:stroke-white/25"
                                >
                                    <path
                                        d="M3 8L6 11L11 3.5"
                                        strokeWidth={2}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        className="opacity-0 group-has-[:checked]:opacity-100"
                                    />
                                    <path
                                        d="M3 7H11"
                                        strokeWidth={2}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        className="opacity-0 group-has-[:indeterminate]:opacity-100"
                                    />
                                </svg>
                                </div>
                            </div>
                            <div className="text-sm/6">
                                <label htmlFor="subtitles" className="font-medium text-white">
                                Subtitles
                                </label>
                                <p id="subtitles-description" className="text-gray-400">
                                    The movie has subtitles.
                                </p>
                            </div>
                        </div>
                        <div className="flex gap-3">
                            <div className="flex h-6 shrink-0 items-center">
                                <div className="group grid size-4 grid-cols-1">
                                <input
                                    id="is_3d"
                                    name="is_3d"
                                    type="checkbox"
                                    checked={data['is_3d']}
                                    onChange={e => setData('is_3d', e.target.checked)}
                                    aria-describedby="is_3d-description"
                                    className="col-start-1 row-start-1 appearance-none rounded border border-white/10 bg-white/5 checked:border-indigo-500 checked:bg-indigo-500 indeterminate:border-indigo-500 indeterminate:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 disabled:border-white/5 disabled:bg-white/10 disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                />
                                <svg
                                    fill="none"
                                    viewBox="0 0 14 14"
                                    className="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-[:disabled]:stroke-white/25"
                                >
                                    <path
                                        d="M3 8L6 11L11 3.5"
                                        strokeWidth={2}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        className="opacity-0 group-has-[:checked]:opacity-100"
                                    />
                                    <path
                                        d="M3 7H11"
                                        strokeWidth={2}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        className="opacity-0 group-has-[:indeterminate]:opacity-100"
                                    />
                                </svg>
                                </div>
                            </div>
                        <div className="text-sm/6">
                            <label htmlFor="is_3d" className="font-medium text-white">
                            3D
                            </label>
                            <p id="is_3d-description" className="text-gray-400">
                                The movie is in 3D.
                            </p>
                        </div>
                        </div>
                        <div className="flex gap-3">
                            <div className="flex h-6 shrink-0 items-center">
                                <div className="group grid size-4 grid-cols-1">
                                <input
                                    id="dubbed"
                                    name="dubbed"
                                    type="checkbox"
                                    checked={data.dubbed}
                                    onChange={e => setData('dubbed', e.target.checked)}
                                    aria-describedby="dubbed-description"
                                    className="col-start-1 row-start-1 appearance-none rounded border border-white/10 bg-white/5 checked:border-indigo-500 checked:bg-indigo-500 indeterminate:border-indigo-500 indeterminate:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 disabled:border-white/5 disabled:bg-white/10 disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                />
                                <svg
                                    fill="none"
                                    viewBox="0 0 14 14"
                                    className="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-[:disabled]:stroke-white/25"
                                >
                                    <path
                                        d="M3 8L6 11L11 3.5"
                                        strokeWidth={2}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        className="opacity-0 group-has-[:checked]:opacity-100"
                                    />
                                    <path
                                        d="M3 7H11"
                                        strokeWidth={2}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        className="opacity-0 group-has-[:indeterminate]:opacity-100"
                                    />
                                </svg>
                                </div>
                            </div>
                            <div className="text-sm/6">
                                <label htmlFor="dubbed" className="font-medium text-white">
                                Dubbed
                                </label>
                                <p id="dubbed-description" className="text-gray-400">
                                    The movie is dubbed.
                                </p>
                            </div>
                        </div>
                    </div>
                    </fieldset>
                </div>
            </div>

    )
}